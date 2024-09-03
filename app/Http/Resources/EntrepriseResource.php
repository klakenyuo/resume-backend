<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EntrepriseResource extends JsonResource
{   
    public function toArray($request)
    {
        return [ 
            'id' => $this->id,
            'name' => $this->name,
            'domain' => $this->domain == ' ' || $this->domain == null ? null : $this->domain,
            'linkedin' => $this->linkedin,
            'adress' => $this->adress,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'website' => $this->website,
            'size' => $this->size,
            'industry' => $this->industry,
            'logo' => $this->logo,
        ];
    }

    
}
