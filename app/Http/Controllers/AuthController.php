<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\WelcomeEmail;
use App\Mail\ForgotPasswordEmail;
use Illuminate\Support\Carbon;
use App\Mail\UpdatePasswordEmail;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public $customMessages = [
        'required' => 'Le champ :attribute est requis.',
        'string' => 'Le champ :attribute doit être une chaîne de caractères.',
        'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
        'email' => 'Le champ :attribute doit être une adresse email valide.',
        'unique' => ':attribute déjà utilisé.',
        'date' => 'Le champ :attribute doit être une date valide.',
        'min' => ':attribute doit contenir au moins :min caractères.',
        'in' => ':attribute doit être dans l\'une des valeurs suivantes : :values.',
        'dimensions' => 'Les dimensions de l\'image :attribute ne sont pas correctes. Les dimensions attendues sont : :width pixels de largeur et :height pixels de hauteur.',
    ];
 
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }
 
        $user = User::where('email', $request->email)->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(array('message' => __("Identifiants incorrects")), 422);
        }

        if(!$user->isActive){
            return response()->json(array('message' => __("Compte bloqué!")), 422);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
    
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Deconnexion réussie!']);
    }
 
    public function me()
    {
        try {
            $user = Auth::user();
            return new UserResource($user);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Token invalide'], 401);
        }
    }
    
    public function register(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ], $this->customMessages);
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        $verification_code = mt_rand(2432, 9999);
         
        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->verification_code = $verification_code;

        if($request->role){
            $user->role = $request->role;
        }

        $user->password = Hash::make($request->password);
        $user->save();

        Mail::to($user->email)->cc('gillesakakpo01@gmail.com')->send(new WelcomeEmail($user));

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function confirmCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|exists:users,verification_code',
        ], $this->customMessages);

        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        $user = auth()->user();

        if($user->verification_code == $request->code){
            $user->email_verified_at = Carbon::now();
            $user->save();
            return response()->json([
                'code' => 200,
                'message' => 'Compte vérifié avec succès!'
            ]);
        }else{
            return response()->json([
                'code' => 401,
                'message' => 'Code invalide!'
            ]);
        }
    }
     
    public function resendCode(Request $request)
    {
        $user = auth()->user();
        $verification_code = mt_rand(2432, 9999);
        $user->verification_code = $verification_code;
        $user->save();

        Mail::to($user->email)->cc('gillesakakpo01@gmail.com')->send(new WelcomeEmail($user));

        return response()->json([
            'code' => 200,
            'message' => 'Code envoyé avec succès!'
        ]);
    }

    public function refreshToken()
    {
        
        try {
            $user = $request->user();
            $user->tokens()->delete();

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Le token Bearer n\'est pas valide.'], 401);
        }
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ], $this->customMessages);

        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), "data" => $errors), 422);
        }

        $email = $request->email;

        $existingRecord = DB::table('password_reset_tokens')->where('email', $email)->first();

        if ($existingRecord) {
            $code = $existingRecord->token;
        } else {
            $code = mt_rand(100000, 999999);

            DB::table('password_reset_tokens')->insert([
                'email' => $email,
                'token' => $code,
                'created_at' => Carbon::now()
            ]);
        }

        $user = User::where('email', $request->email)->first();
        
        Mail::to($user->email)->cc('gillesakakpo01@gmail.com')->send(new ForgotPasswordEmail($code));

        return response()->json([
            'code' => 200,
            'message' => 'Nous vous avons envoyé un code de réinitialisation!'
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6',
            'code' => 'required|integer',
        ], $this->customMessages);

        $errors = $validator->errors();

        if ($errors->count()) {
            $response = array('code' => 422, 'message' => $errors->first(), "data" => $errors);
            return response()->json($response, 422);
        }


        $updatePassword = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->code
            ])
            ->first();


        if (!$updatePassword) {
            $response = array('code' => 422, 'message' => __("Code invalide!"));
            return response()->json($response, 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);
        
        // DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();
        Mail::to($user->email)->send(new UpdatePasswordEmail($user));

        return response()->json([
            'code' => 200,
            'message' => 'Mot de passe reinitialisé!'
        ]);
    }
 
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6|confirmed',
            'new_password_confirmation' => 'required|string|min:6', 
        ], $this->customMessages);

        $errors = $validator->errors();

        if ($errors->count()) {
            $response = array('code' => 422, 'message' => $errors->first(), "data" => $errors);
            return response()->json($response, 422);
        }

        $user = Auth::user();

        // Vérifier si l'ancien mot de passe est correct
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'code' => 401,
                'message' => 'Mot de passe actuel incorrect!'
            ]);
        }

        // Mettre à jour le mot de passe
        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json([
            'code' => 200,
            'message' => 'Mot de passe changé avec succès!'
        ]);
    }
    
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'string',
            'last_name' => 'string',
            'telephone' => 'string',
            'address' => 'string',
            'birth_date' => 'date',
            'exp_years' => 'integer',
            'linkedin' => 'string',
            'birth_date' => 'nullable|string',
            'birth_place' => 'nullable|string',
            'nationality' => 'nullable|string',
            'iban' => 'nullable|string',
            'tel_one' => 'nullable|string',
            'tel_two' => 'nullable|string',
        ], $this->customMessages);

        $errors = $validator->errors();

        if ($errors->count()) {
            $response = array('code' => 422, 'message' => $errors->first(), "data" => $errors);
            return response()->json($response, 422);
        }

        $validated_data = $validator->validated();

        $user = Auth::user();
        $user->fill($validated_data);
        
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo'); 
            $user->clearMediaCollection('photo');  
            $user->addMedia($request->file('photo'))->toMediaCollection('photo');
        }
        
        $user->save();
        return response()->json([
            'code' => 200,
            'message' => 'Profil mis à jour avec succès',
            'data' => new UserResource($user)
        ]);
    }

}