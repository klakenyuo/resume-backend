<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Timesheet;
use App\Models\TimesheetEntry;
use App\Models\TimesheetEntryProject;


class TimesheetFullResource extends JsonResource
{   
    public function toArray($request)
    {
        $update = $this->update_timesheet();
        if($update){
            return [  
                'id' => $this->id,
                'user_id' => $this->user_id,
                'year' => $this->year,
                'month' => $this->month,
                'month_label' => $this->getMonthLabel(),
                'status' => $this->status,
                'status_label' => $this->getStatusLabel(),
                'status_color' => $this->getStatusColor(),
                'comment'=> $this->comment,
                'user' => New UserResourceLite($this->user),
                'total_work_duration' => $this->total_work_duration,
                'effective_work_duration' => $this->effective_work_duration,
                'total_work_duration_day' => $this->total_work_duration_day,
                'total_work_duration_day_formatted' => $this->total_work_duration_day_formatted,
                // 10 Juillet 2021 for updated_at in french
                'updated_at' => $this->updated_at->format('d F Y'),
                'entries' => $this->timesheetEntries ?  TimesheetEntryResource::collection($this->timesheetEntries) : null,
            ];  
        }
    }

    // update the timesheet
    public function update_timesheet()
    {
      $timesheet_id = $this->id;
      $user_id = $this->user_id;
      $timesheet = Timesheet::find($timesheet_id);
      $month = $timesheet->month;
      $year = $timesheet->year;
      $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

      if($timesheet){
        $projects = $this->user->projects;

        for($i = 1; $i <= $daysInMonth; $i++){

            // $timesheetEntry = new TimesheetEntry();
            // get entry where day id $i and date $year-$month-$i
            $timesheetEntry = TimesheetEntry::where('day', $i)->where('date', $year.'-'.$month.'-'.$i)->first();
            if($timesheetEntry){
                foreach($projects as $project){
                    // get entry project where timesheet entry id $timesheetEntry->id and project id $project->id
                    $timesheetEntryProject = TimesheetEntryProject::where('timesheet_entry_id', $timesheetEntry->id)->where('project_id', $project->id)->first();
                    if(!$timesheetEntryProject){
                        $timesheetEntryProject = new TimesheetEntryProject();
                        $timesheetEntryProject->timesheet_entry_id = $timesheetEntry->id;
                        $timesheetEntryProject->project_id = $project->id;
                        $timesheetEntryProject->work_duration = 0;
                        $timesheetEntryProject->save();
                    }
                }
            }
        }
      }
      return true;
      
    }


    public function total_eff_work_duration(){
        $total_eff_work_duration = 0;
        foreach($this->timesheetEntries as $entry){
            $total_eff_work_duration += $entry->effective_work_duration;
        }
        return $total_eff_work_duration;
    }

    // total_work_duration
    public function total_work_duration(){
        $total_work_duration = 0;
        foreach($this->timesheetEntries as $entry){
            $total_work_duration += $entry->work_duration;
        }
        return $total_work_duration;
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

    // status label :  'pending', 'approved', 'rejected' in french
    public function getStatusLabel(){
        $status = $this->status;
        $statusLabels = array(
            'pending'=> 'En attente',
            'approved'=> 'Approuvé',
            'rejected'=> 'Rejeté',
            'pending_validation'=> 'En attente de validation',
        );
        if(!array_key_exists($status, $statusLabels)){
            return 'En attente';
        }
        return $statusLabels[$status];
    }

    // status color :  'pending', 'approved', 'rejected' in french
    public function getStatusColor(){
        $status = $this->status;
        $statusColors = array(
            'pending'=> 'orange',
            'approved'=> 'green',
            'rejected'=> 'red'
        );
        if(!array_key_exists($status, $statusColors)){
            return 'orange';
        }
        return $statusColors[$status];
    }


    
}
