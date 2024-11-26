<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TimesheetEntryProjectResource extends JsonResource
{   
    public function toArray($request)
    {
        return [  
            'id' => $this->id,
            'project_id' => $this->project_id,
            'work_duration' => $this->work_duration,
            'project' => New ProjectResourceLite($this->project),
            'timesheet_entry_id' => $this->timesheet_entry_id,
            'can_effective' => $this->project->is_paid ? true : false,  
            // 'can_effective' => $this->can_effective(),  
        ];  
    }

    

    public function can_effective(){
        // if project title is 'Absence Non Maladie' return false else return true
        $project = $this->project;
        if($project->title == 'Absence Non Maladie'){
            return false;
        }
        return true;
    }

   
    
}
