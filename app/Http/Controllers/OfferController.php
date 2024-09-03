<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\OfferResource; 
use App\Models\Offer;
use App\Models\Entreprise;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
 
class OfferController extends Controller
{

    public $customMessages = [
        'required' => 'Le champ :attribute est requis.',
        'string' => 'Le champ :attribute doit être une chaîne de caractères.',
        'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
        'email' => 'Le champ :attribute doit être une adresse email valide.',
        'unique' => 'Le  champ :attribute est déjà utilisé.',
        'date' => 'Le champ :attribute doit être une date valide.',
        'min' => 'Le champ :attribute doit contenir au moins :min caractères.',
        'in' => 'Le champ :attribute doit être l\'une des valeurs suivantes : :values.',
    ];

//    crud for offers

    public function index(Request $request)
    {
        // get offers order by id desc paginated 10
        $offers = Offer::orderBy('id', 'desc')->paginate(10);
        return OfferResource::collection($offers);
    }

    // store
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // offers(id,title,description,experience_years,category,country,city,remote?,type,image,industry) type CDD or CDI
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'experience_years' => 'required|integer',
            'category' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'remote' => 'nullable|boolean',
            'type' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        $offer = Offer::create([
            'title' => $request->title,
            'description' => $request->description,
            'experience_years' => $request->experience_years,
            'category' => $request->category,
            'country' => $request->country,
            'city' => $request->city,
            'type' => $request->type,
            'industry' => $request->industry,
        ]);

        // if photo add photo to media 
        if($request->hasFile('photo')){
            $offer->addMedia($request->file('photo'))->toMediaCollection('photo');
        }

        $offer->save();


        return response()->json(array('message' => __("Offer ajouté"), 'data' => new OfferResource($offer)), 200);
    }

    // update
    public function update(Request $request, $id)
    {
        $offer = Offer::find($id);

        if(!$offer){
            return response()->json(array('message' => __("Offer introuvable")), 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'experience_years' => 'required|integer',
            'category' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

            //    fill the request data
        $offer->fill($request->all());

        // if photo add photo to media
        if($request->hasFile('photo')){
            $offer->addMedia($request->file('photo'))->toMediaCollection('photo');
        }
        $offer->save();

        return response()->json(array('message' => __("Offer modifié"), 'data' => new OfferResource($offer)), 200);
    }

    // delete
    public function destroy($id)
    {
        $offer = Offer::find($id);
        if(!$offer){
            return response()->json(array('message' => __("Offer introuvable")), 404);
        }

        $offer->delete();

        return response()->json(array('message' => __("Offer supprimé")), 200);
    }

    
    public function search(Request $request)
    {
        $search = $request->search;
        $entreprises = Offer::where('title', 'like', '%'.$search.'%')
            ->orWhere('description', 'like', '%'.$search.'%')
            ->orWhere('category', 'like', '%'.$search.'%')
            ->orWhere('industry', 'like', '%'.$search.'%')
            ->paginate(10);
        return OfferResource::collection($entreprises);
    }


}