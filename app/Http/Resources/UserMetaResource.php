<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserMetaResource extends JsonResource
{   
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'label' => $this->label,
            'description' => $this->description,
        ];
    }
}
