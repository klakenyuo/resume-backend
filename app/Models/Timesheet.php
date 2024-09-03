<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Timesheet extends Model 
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'user_id',
        'status',
        'comment',
        'month',
        'year',
    ];

    protected $appends = ['effective_work_duration','total_work_duration','total_work_duration_day','total_work_duration_day_formatted'];

    public function getTotalWorkDurationDayFormattedAttribute()
    {
        $duration = $this->total_work_duration_day;
        $label = $duration > 1 ? 'jours' : 'jour';
        return $duration.' '.$label;
    }

    public function getTotalWorkDurationDayAttribute()
    {
        $duration = $this->total_work_duration;
        $duration = round($duration / 16);
        return $duration;
    }

    public function getEffectiveWorkDurationAttribute()
    {
        $duration = 0;
        foreach($this->timesheetEntries as $entry){
            $duration += $entry->effective_work_duration;
        }
        return $duration;
    }

    public function getTotalWorkDurationAttribute()
    {
        $duration = 0;
        foreach($this->timesheetEntries as $entry){
            $duration += $entry->total_work_duration;
        }
        return $duration;
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function timesheetEntries()
    {
        return $this->hasMany(TimesheetEntry::class);
    }
     
}
