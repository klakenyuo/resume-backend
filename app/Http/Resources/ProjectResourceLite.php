<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResourceLite extends JsonResource
{   
    public function toArray($request)
    {
        return [  
            'id' => $this->id,
            'title' => $this->title,
            'client' => $this->client,
            'status' => $this->status,
            'is_paid' => $this->is_paid,
        ];  
    }


    
}
