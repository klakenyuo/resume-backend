<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{   
    public function toArray($request)
    {
        return [  
            // name,website,domain,country,city,adress,contact,telephone,email,logo
            'id' => $this->id,
            'name' => $this->name,
            'website' => $this->website,
            'domain' => $this->domain,
            'country' => $this->country,
            'city' => $this->city,
            'adress' => $this->adress,
            'contact' => $this->contact,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'logo' => $this->logo,
            'logoImg'=> $this->logoImg,
        ];  
    }


    
}
