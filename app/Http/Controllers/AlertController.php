<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\AlertResource; 
use App\Models\Alert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
 
class AlertController extends Controller
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

    //crud for alerts

    public function index(Request $request)
    {
        // get alerts order by id desc paginated 10
        $alerts = Alert::orderBy('id', 'desc')->paginate(10);
        return AlertResource::collection($alerts);
    }

    // store
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'limit_date' => 'nullable|string',
            'limit_time' => 'nullable|string',
            'status' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        $user_id = Auth::user()->id;

        $alert = Alert::create([
            'title' => $request['title'],
            'description' => $request['description'],
            'limit_date' => $request['limit_date'],
            'limit_time' => $request['limit_time'],
            'status' => $request['status'],
            'user_id' => $user_id,
        ]);

        $alert->save();


        return response()->json(array('message' => __("Alert ajouté"), 'data' => new AlertResource($alert)), 200);
    }

    // update
    public function update(Request $request, $id)
    {
        $alert = Alert::find($id);

        if(!$alert){
            return response()->json(array('message' => __("Alert introuvable")), 404);
        }

        $validator = Validator::make($request->all(), [
            'title'=>'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'limit_date' => 'nullable|date',
            'limit_time' => 'nullable|string',
            'status' => 'nullable|string|max:255',
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        $alert->fill($request->all());

        $alert->save();

        return response()->json(array('message' => __("Alert modifié"), 'data' => new AlertResource($alert)), 200);
    }

    // delete
    public function destroy($id)
    {
        $alert = Alert::find($id);
        if(!$alert){
            return response()->json(array('message' => __("Alert introuvable")), 404);
        }

        $alert->delete();

        return response()->json(array('message' => __("Alert supprimé")), 200);
    }

   
    public function search(Request $request)
    {
        $search = $request->search;
        $entreprises = Alert::where('title', 'like', '%'.$search.'%')
            ->orWhere('description', 'like', '%'.$search.'%')
            ->paginate(10);
        return AlertResource::collection($entreprises);
    }

    // show
    public function show($id)
    {
        $alert = Alert::find($id);
        if(!$alert){
            return response()->json(array('message' => __("Alert introuvable")), 404);
        }
        return new AlertResource($alert);
    }


}