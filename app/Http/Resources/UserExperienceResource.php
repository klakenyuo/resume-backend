<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserExperienceResource extends JsonResource
{   
    public function toArray($request)
    {
        return [ 
            'id' => $this->id,
            'entreprise' => $this->entreprise,
            'title' => $this->title,
            'project' => $this->project,
            'start_date' => $this->formatDates($this->start_date),
            'end_date' => $this->formatDates($this->end_date),
            'duration' => $this->duration(),
            'mission' => $this->mission,
            'envs' => $this->envs,
            'user_id' => $this->user_id,
        ];
    }

    // format the data to be returned month and year

    public function formatDates($date)
    {
        return $date;
        return date('F Y', strtotime($date));
    }

    public function duration()
    {
        $start = strtotime($this->start_date);
        $end = strtotime($this->end_date);
        $diff = $end - $start;
        $days = $diff / (60 * 60 * 24);
        $months = $days / 30;
        // round to the nearest month
        $months = round($months);
        $years = 0;        
        // if more than 12 months
        if ($months >= 12) {
            $years = floor($months / 12);
            $months = $months % 12;
        }
        // return in french
        if ($years > 0) {
            return $years . ' ans et ' . $months . ' mois';
        } else {
            return $months . ' mois';
        }
    }
}
