<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Http\Resources\UserResource; 
use App\Http\Resources\UserExperienceResource;
use App\Models\User;
use App\Models\UserExperience;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;


 
class UserExperienceController extends Controller
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

    // get the authenticated user experience
    public function getMyExperiences()
    {
        $user = Auth::user();
        $experiences = $user->experiences;
        return response()->json(['data' => UserExperienceResource::collection($experiences)], 200);
    }

    // create a new experience for the authenticated user
    public function createExperience(Request $request)
    {
        $validator = Validator::make($request->all(), [
             'entreprise' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'project' => 'string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'mission' => 'required|string',
            'envs' => 'string',
        ], $this->customMessages);

        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(['message' => $errors->first()], 400);
        }

        $user = Auth::user();
        $experience = new UserExperience();
        $experience->entreprise = $request->entreprise;
        $experience->title = $request->title;
        $experience->project = $request->project;
        $experience->start_date = $request->start_date;
        $experience->end_date = $request->end_date;
        $experience->mission = $request->mission;
        $experience->envs = $request->envs;

        $experience->user_id = $user->id;
        $experience->save();

        return response()->json(['data' => new UserExperienceResource($experience)], 201);
    }

    // update a experience for the authenticated user
    public function updateExperience(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'entreprise' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'project' => 'string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'mission' => 'required|string',
            'envs' => 'string',
        ], $this->customMessages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = Auth::user();
        $experience = UserExperience::find($id);

        if (!$experience) {
            return response()->json(['error' => 'Experience not found'], 404);
        }

        if ($experience->user_id != $user->id) {
            return response()->json(['error' => 'You are not allowed to update this experience'], 403);
        }

        $experience->entreprise = $request->entreprise;
        $experience->title = $request->title;
        $experience->project = $request->project;
        $experience->start_date = $request->start_date;
        $experience->end_date = $request->end_date;
        $experience->mission = $request->mission;
        $experience->envs = $request->envs;
        $experience->save();

        return response()->json(['data' => new UserExperienceResource($experience)], 200);
    }

    // delete a experience for the authenticated user
    public function deleteExperience($id)
    {
        $user = Auth::user();
        $experience = UserExperience::find($id);

        if (!$experience) {
            return response()->json(['error' => 'Experience not found'], 404);
        }

        if ($experience->user_id != $user->id) {
            return response()->json(['error' => 'You are not allowed to delete this experience'], 403);
        }

        $experience->delete();

        return response()->json(['message' => 'Experience deleted'], 200);
    }



    

}