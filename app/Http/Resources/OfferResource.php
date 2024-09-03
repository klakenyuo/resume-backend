<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{   
    public function toArray($request)
    {
        return [  
            // offers(id,title,description,experience_years,category,country,city,remote?,type,image,industry,status) type CDD or CDI
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'experience_years' => $this->experience_years,
            'category' => $this->category,
            'country' => $this->country,
            'city' => $this->city,
            'status' => $this->status,
            'remote' => $this->remote,
            'type' => $this->type,
            'industry' => $this->industry,
            'photo' => $this->photo,
            'photoImg' => $this->photoImg,
        ];  
    }


    
}
