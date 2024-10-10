<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfilResource extends JsonResource
{   
    public function toArray($request)
    {
        return [  
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'entreprise_id' => $this->entreprise_id,
            'entreprise' => $this->entreprise->name ?? '',
            'title' => $this->title,
            'linkedin' => $this->linkedin,
            'telephone' => $this->telephone,
            'telephone_two' => $this->telephone_two,
            'email_two' => $this->email_two,
            'website' => $this->website,
            'adress' => $this->adress,
            'city' => $this->city,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'about' => $this->about,
            'photo' => $this->photo,
            'enrich_status' => $this->enrich_status,
            'can_enrich' => $this->can_enrich,
            'tags'=>$this->tags,
            'tags_labels'=>$this->tags_labels,
            'is_enrich_cout'=>$this->is_enrich_cout,
        ];  
    }


    
}
