<?php

namespace App\Http\Resources;
use App\Models\Timesheet;

use Illuminate\Http\Resources\Json\JsonResource;

class MonthYearResource extends JsonResource
{   
    public function toArray($request)
    {
        return [  
            'year' => $this->year,
            'month' => $this->month,
            'month_label' => $this->getMonthLabel(),
            'total' => $this->total,
            'pending_total' => $this->pending_total(),
        ];  
    }

    // get month label by number in french
    public function getMonthLabel(){
        $month = $this->month;
        $month = intval($month);
        $months = array(
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre'
        );
        return $months[$month];
    }

    // get total number of timesheets for status pending_validation
    public function pending_total(){
        //    pending_total for this month and year
        $pending_total = Timesheet::where('year', $this->year)
            ->where('month', $this->month)
            ->where('status', 'pending_validation')
            ->count();

        return $pending_total;
    }


    
}
