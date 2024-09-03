<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TimesheetEntryResource extends JsonResource
{   
    public function toArray($request)
    {
        return [  
            'id' => $this->id,
            'timesheet_id' => $this->timesheet_id,
            'date' => $this->date,
            'day' => $this->day,
            'day_label'=> $this->getDay(),
            'can_edit'=> $this->can_edit(),
            'work_duration' => $this->work_duration,
            'timesheet_entry_projects' => $this->timesheetEntryProjects ?  TimesheetEntryProjectResource::collection($this->timesheetEntryProjects) : null,
        ];  
    }

    // get day by date like Lun if date is Monday in french
    public function getDay(){
        $date = $this->date;
        $days = array('Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam');
        $day = $days[date('w', strtotime($date))];
        return $day;
    }
    // can edit if not weekend
    public function can_edit(){
        
        $day = $this->getDay();
        // if($day == 'Dim' || $day == 'Sam'){
        //     return true;
        // }

        if($this->timesheet->status == 'approved'){
            return false;
        }

        // if auth role id admin cant edit
        if(auth()->user()->role == 'admin' && $this->timesheet->user->id !== auth()->user()->id){
            return false;
        }

        return true;
    }


    
}
