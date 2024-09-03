<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TimesheetEntryProject extends Model 
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'timesheet_entry_id',
        'project_id',
        'work_duration',
    ];

    public function timesheetEntry()
    {
        return $this->belongsTo(TimesheetEntry::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    

   
}
