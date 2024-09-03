<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Http\Resources\UserResource; 
use App\Http\Resources\UserMetaResource;
use App\Models\User;
use App\Models\UserMeta;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;


 
class UserMetaController extends Controller
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

    public function getMyCertifications()
    {
        $user = Auth::user();
        $certifications = $user->certifications; 
        return response()->json(['data' => UserMetaResource::collection($certifications)], 200);
    }

    // get my expertises
    public function getMyExpertises()
    {
        $user = Auth::user();
        $expertises = $user->expertises; 
        return response()->json(['data' => UserMetaResource::collection($expertises)], 200);
    }

    // get my interests
    public function getMyInterests()
    {
        $user = Auth::user();
        $interests = $user->interests; 
        return response()->json(['data' => UserMetaResource::collection($interests)], 200);
    }

    // get my skills
    public function getMySkills()
    {
        $user = Auth::user();
        $skills = $user->skills; 
        return response()->json(['data' => UserMetaResource::collection($skills)], 200);
    }

     // get my skills
     public function getMyTSkills()
     {
         $user = Auth::user();
         $skills = $user->tskills; 
         return response()->json(['data' => UserMetaResource::collection($skills)], 200);
     }

     public function getMyMSkills()
     {
         $user = Auth::user();
         $skills = $user->mskills; 
         return response()->json(['data' => UserMetaResource::collection($skills)], 200);
     }

    // create a new meta for the authenticated user
    public function createMeta(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'type' => 'string|max:255',
        ], $this->customMessages);

        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(['message' => $errors->first()], 400);
        }

        $user = Auth::user();
        $meta = new UserMeta();
        $meta->label = $request->label;
        $meta->description = $request->description ?? '';
        $meta->type = $request->type;
        $meta->user_id = $user->id;
        $meta->save();

        return response()->json(['data' => new UserMetaResource($meta)], 201);
    }

    // update a meta for the authenticated user
    public function updateMeta(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'description' => 'string|max:255',
            'type' => 'string|max:255',
        ], $this->customMessages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = Auth::user();
        $meta = UserMeta::find($id);

        if (!$meta) {
            return response()->json(['error' => 'Meta not found'], 404);
        }

        if ($meta->user_id != $user->id) {
            return response()->json(['error' => 'You are not allowed to update this meta'], 403);
        }

        $meta->label = $request->label;
        $meta->description = $request->description;
        $meta->save();

        return response()->json(['data' => new UserMetaResource($meta)], 200);
    }

    // delete a meta for the authenticated user
    public function deleteMeta($id)
    {
        $user = Auth::user();
        $meta = UserMeta::find($id);

        if (!$meta) {
            return response()->json(['error' => 'Meta not found'], 404);
        }

        if ($meta->user_id != $user->id) {
            return response()->json(['error' => 'You are not allowed to delete this meta'], 403);
        }

        $meta->delete();

        return response()->json(['message' => 'Meta deleted'], 200);
    }



    

}