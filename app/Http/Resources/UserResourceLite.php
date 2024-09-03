<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResourceLite extends JsonResource
{   
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->first_name.' '.$this->last_name,
            'email' => $this->email,
            'linkedin' => $this->linkedin,
            'role' => $this->role,
            'photo' => $this->photo,
            'photoImg' => $this->photoImg,
            'isActive' => $this->isActive,
            'projects'=> ProjectResourceLite::collection($this->projects),
        ];
    }
}
