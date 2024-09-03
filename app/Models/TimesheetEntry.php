<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TimesheetEntry extends Model 
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'timesheet_id',
        'date',
        'work_duration',
    ];



    // append effective_work_duration
    protected $appends = ['effective_work_duration','total_work_duration'];

    public function timesheet()
    {
        return $this->belongsTo(Timesheet::class);
    }

    public function timesheetEntryProjects()
    {
        return $this->hasMany(TimesheetEntryProject::class);
    }

    public function getEffectiveWorkDurationAttribute()
    {
        $duration = 0;
        foreach($this->timesheetEntryProjects as $project){
            if(!$project->title=="Absence Non Maladie"){
                $duration += $project->work_duration;
            }
        }
        return $duration;
    }

     public function getTotalWorkDurationAttribute()
    {
        $duration = 0;
        foreach($this->timesheetEntryProjects as $project){
            $duration += $project->work_duration;
        }
        return $duration;
    }


}
