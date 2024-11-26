<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{   
    public function toArray($request)
    {
        return [
            // first_name,last_name, email,telephone, address,birth_date,exp_years ,role, password, remember_token, linkedin, verification_code, isActive, isAdmin, email_verified_at
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'address' => $this->address,
            'birth_date' => $this->birth_date,
            'exp_years' => $this->exp_years,
            'role' => $this->role,
            'linkedin' => $this->linkedin,
            'verification_code' => $this->verification_code,
            'isActive' => $this->isActive,
            'isAdmin' => $this->isAdmin,
            'email_verified_at' => $this->email_verified_at,
            'photo' => $this->photo,
            'photoImg' => $this->photoImg,
            'society' => $this->society,
            'entry_date' => $this->entry_date,
            'birth_place' => $this->birth_place,
            'nationality' => $this->nationality,
            'adress' => $this->adress,
            'iban' => $this->iban,
            'tel_one' => $this->tel_one,
            'tel_two' => $this->tel_two,
            'comments' => $this->comments,
            'tjm' => $this->tjm,
            'permissions' =>  PermissionResource::collection($this->permissions) ,
            'permissions_name' => $this->get_permissions_name() ,
            'is_login_office' => $this->is_login_office,
            'color'=>$this->my_color(),
            'initials'=>$this->my_initials(),
        ];
    }

    // format permission as array of permission name only 
    public function get_permissions_name()
    {
        $permissions = [];
        foreach ($this->permissions as $permission) {
            $permissions[] = $permission->name;
        }

        if(!$permissions){
            return ['timesheet'];
        }
        return $permissions;
    }
}
