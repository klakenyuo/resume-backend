<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResumeResourceLite; 
use App\Http\Resources\ResumeResource; 

use App\Models\Resume;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
 
class ResumeController extends Controller
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

    public function index(Request $request)
    {
        $resumes = Resume::paginate(10);
        return ResumeResource::collection($resumes);
    }

    // get resume by candidat id
    public function getResumeByCandidatId($candidat_id)
    {
        $resume = Resume::where('candidat_id', $candidat_id)->get();
        return ResumeResource::collection($resume);
    }

    // store
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'candidat_id' => 'required|string|max:255',
            'pseudo' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'content' => 'nullable|string|max:255',
            'content_en' => 'nullable|string|max:255',
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }
        // check if role is set in the request

        $resume = Resume::create([
            'candidat_id' => $request['candidat_id'],
            'pseudo' => $request['pseudo'],
            'description' => $request['description'],
            'content' => $request['content'],
            'content_en' => $request['content_en'],
        ]);

        $resume->save();


        return response()->json(array('message' => __("Resume ajouté"), 'data' => new ResumeResourceLite($resume)), 200);
    }

    // update
    public function update(Request $request, $id)
    {
        $resume = Resume::find($id);

        if(!$resume){
            return response()->json(array('message' => __("Resume introuvable")), 404);
        }

        $validator = Validator::make($request->all(), [
            'candidat_id' => 'required|string|max:255',
            'pseudo' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'content' => 'nullable|string|max:255',
            'content_en' => 'nullable|string|max:255',
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        $validated_data = $validator->validated();

        $resume->fill($validated_data);
        $resume->save();

        return response()->json(array('message' => __("Mise à jour effectuée"), 'data' => new ResumeResourceLite($resume)), 200);
    }

    // delete
    public function destroy($id)
    {
        $resume = Resume::find($id);
        if(!$resume){
            return response()->json(array('message' => __("Resume introuvable")), 404);
        }

        $resume->delete();
        return response()->json(array('message' => __("Resume supprimé")), 200);
    }


    public function search(Request $request)
    {
        $search = $request->search;
        $resumes = Resume::where('pseudo', 'like', '%'.$search.'%') 
            ->paginate(10);
        return ResumeResourceLite::collection($resumes);
    } 
    

}