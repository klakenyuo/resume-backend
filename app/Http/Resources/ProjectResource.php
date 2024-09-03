<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{   
    public function toArray($request)
    {
        return [  
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'client' => $this->client,
            'status' => $this->status,
            'users' => UserResourceLite::collection($this->users),
        ];  
    }


    
}
