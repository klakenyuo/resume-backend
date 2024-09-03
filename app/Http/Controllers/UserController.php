<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResourceLite; 
use App\Http\Resources\UserResource; 
use App\Http\Resources\PermissionResource;
use App\Models\User;
use App\Models\UserPermission;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
 
class UserController extends Controller
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

//    crud for users

    public function index(Request $request)
    {
        $users = User::paginate(10);
        return UserResource::collection($users);
    }

    // store
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }
        // check if role is set in the request

        $user = User::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
            'role' => $request['role'],
            'password' =>  Hash::make($request['password']),
        ]);

        if($request->hasFile('photo')){
            $user->addMedia($request->file('photo'))->toMediaCollection('photo');
        }

        $user->save();


        return response()->json(array('message' => __("User ajouté"), 'data' => new UserResourceLite($user)), 200);
    }

    // update
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if(!$user){
            return response()->json(array('message' => __("User introuvable")), 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'tjm' => 'nullable|string|max:255',
            'adress' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'birth_date' => 'nullable|string|max:255',
            'entry_date' => 'nullable|string|max:255',
            'birth_place' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
            'tel_one' => 'nullable|string|max:255',
            'tel_two' => 'nullable|string|max:255',
            'society'   => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'role' => 'required|string|max:255',
            'permissions' => 'nullable|string',
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

            //    fill the request data
        // $user->fill($request->all());
        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];
        $user->email = $request['email'];
        $user->role = $request['role'];

        $user->tjm = $request['tjm'];
        $user->adress = $request['address'];
        $user->address = $request['address'];
        $user->birth_date = $request['birth_date'];
        $user->entry_date = $request['entry_date'];
        $user->birth_place = $request['birth_place'];
        $user->iban = $request['iban'];
        $user->tel_one = $request['tel_one'];
        $user->tel_two = $request['tel_two'];
        $user->society = $request['society'];
        $user->nationality = $request['nationality'];

        $permissions = $request['permissions'];
        $permissions = explode(',', $permissions);

        // delete all permissions from user
        UserPermission::where('user_id', $user->id)->delete();
        if($permissions){
            // add permissions to user
            foreach ($permissions as $permission) {
                if($permission != ''){
                    UserPermission::create([
                        'user_id' => $user->id,
                        'permission_id' => $permission,
                    ]);
                }
            }
        }


        // if photo
        if($request->hasFile('photo')){
            $user->addMedia($request->file('photo'))->toMediaCollection('photo');
        }


        $user->save();

        return response()->json(array('message' => __("Mise à jour effectuée"), 'data' => new UserResourceLite($user)), 200);
    }

    // delete
    public function destroy($id)
    {
        $user = User::find($id);
        if(!$user){
            return response()->json(array('message' => __("User introuvable")), 404);
        }

        $user->delete();
        return response()->json(array('message' => __("User supprimé")), 200);
    }


    public function search(Request $request)
    {
        $search = $request->search;
        $users = User::where('first_name', 'like', '%'.$search.'%')
            ->orWhere('last_name', 'like', '%'.$search.'%')
            ->orWhere('telephone', 'like', '%'.$search.'%')
            ->orWhere('email', 'like', '%'.$search.'%')
            ->orWhere('role', 'like', '%'.$search.'%')
            ->paginate(10);
        return UserResourceLite::collection($users);
    }
    

    // getAllUsers

    public function getAllUsers()
    {
        $users = User::all();
        return UserResourceLite::collection($users);
    }

    // getAllPermissions

    public function getAllPermissions()
    {
        $permissions = Permission::all();
        return PermissionResource::collection($permissions);
    }
    
    

}