<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AlertResource extends JsonResource
{   
    public function toArray($request)
    {
        return [  
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'limit_date' => $this->limit_date,
            'limit_time' => $this->limit_time,
            'status' => $this->status,
            'user_id' => $this->user_id,
            // 'user'=> UserResourceLite::collection($this->user)
        ];  
    }
    
}
