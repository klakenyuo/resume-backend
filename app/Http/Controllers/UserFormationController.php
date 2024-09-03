<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Http\Resources\UserResource; 
use App\Http\Resources\UserFormationResource;
use App\Models\User;
use App\Models\UserFormation;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;


 
class UserFormationController extends Controller
{

    public $customMessages = [
        'required' => 'Le champ :attribute est requis.',
        'string' => 'Le champ :attribute doit être une chaîne de caractères.',
        'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
        'email' => 'Le champ :attribute doit être une adresse email valide.',
        'unique' => 'Le  champ :attribute est déjà utilisée.',
        'date' => 'Le champ :attribute doit être une date valide.',
        'min' => 'Le champ :attribute doit contenir au moins :min caractères.',
        'in' => 'Le champ :attribute doit être l\'une des valeurs suivantes : :values.',
    ];

    // get the authenticated user formation
    public function getMyFormations()
    {
        $user = Auth::user();
        $formations = $user->formations;
        // order formations by year
        $formations = $formations->sortByDesc('year');
        return response()->json(['data' => UserFormationResource::collection($formations)], 200);
    }

    // create a new formation for the authenticated user
    public function createFormation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'address' => 'string|max:255',
            'year' => 'string|max:255',
        ], $this->customMessages);

        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(['message' => $errors->first()], 400);
        }

        $user = Auth::user();
        $formation = new UserFormation();
        $formation->label = $request->label;
        $formation->address = $request->address;
        $formation->year = $request->year;
        $formation->user_id = $user->id;
        $formation->save();

        return response()->json(['data' => new UserFormationResource($formation)], 201);
    }

    // update a formation for the authenticated user
    public function updateFormation(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'address' => 'string|max:255',
            'year' => 'string',
        ], $this->customMessages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = Auth::user();
        $formation = UserFormation::find($id);

        if (!$formation) {
            return response()->json(['error' => 'Formation not found'], 404);
        }

        if ($formation->user_id != $user->id) {
            return response()->json(['error' => 'You are not allowed to update this formation'], 403);
        }

        $formation->label = $request->label;
        $formation->address = $request->address;
        $formation->year = $request->year;
        $formation->save();

        return response()->json(['data' => new UserFormationResource($formation)], 200);
    }

    // delete a formation for the authenticated user
    public function deleteFormation($id)
    {
        $user = Auth::user();
        $formation = UserFormation::find($id);

        if (!$formation) {
            return response()->json(['error' => 'Formation not found'], 404);
        }

        if ($formation->user_id != $user->id) {
            return response()->json(['error' => 'You are not allowed to delete this formation'], 403);
        }

        $formation->delete();

        return response()->json(['message' => 'Formation deleted'], 200);
    }



    

}