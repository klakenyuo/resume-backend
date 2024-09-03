<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserFormationResource extends JsonResource
{   
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'address' => $this->address,
            'user_id' => $this->user_id,
            'year' => $this->year,
            'desc'=>$this->label." , ".$this->address." , ".$this->year
        ];
    }
}
