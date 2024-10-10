<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResumeResource extends JsonResource
{   
    public function toArray($request)
    {
        return [  
            'id'=> $this->id,
            'candidat_id' => $this->candidat_id,
            'pseudo' => $this->pseudo,
            'description' => $this->description,
            'content'=> $this->content,
            'content_en'=> $this->content_en,
        ];  
    }


    
}
