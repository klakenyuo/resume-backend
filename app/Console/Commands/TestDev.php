<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Post;
use Illuminate\Support\Str;
use App\Http\Controllers\NotificationController;
use App\Models\User;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Log;

class TestDev extends Command
{
    protected $signature = 'test:dev';
    protected $description = 'test dev';

    public function handle()
    {
        $this->info("Start testing email");

        $email = "gillesakakpo01@gmail.com";

        $user = User::first();

        Mail::to($email)->send(new WelcomeEmail($user));

    } 

    public function update_timesheet($timesheet)
    {
       //laravel log 
       $this->info('update_timesheet');

      $timesheet_id = $timesheet->id;
      $user_id = $timesheet->user_id;
      $timesheet = Timesheet::find($timesheet_id);
      $month = $timesheet->month;
      $year = $timesheet->year;
      $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

      if($timesheet){
       $projects = $timesheet->user->projects;
       
       for($i = 1; $i <= $daysInMonth; $i++){
           // $timesheetEntry = new TimesheetEntry();
           // get entry where day id $i and date $year-$month-$i
           $timesheetEntry = TimesheetEntry::where('day', $i)->where('date', $year.'-'.$month.'-'.$i)->first();
           if($timesheetEntry){
               foreach($projects as $project){
                   // get entry project where timesheet entry id $timesheetEntry->id and project id $project->id
                   $timesheetEntryProject = TimesheetEntryProject::where('timesheet_entry_id', $timesheetEntry->id)->where('project_id', $project->id)->first();
                   if(!$timesheetEntryProject){
                       // log project not exist
                       $this->info('project not exist '.$timesheetEntry->id.' and project : '.$project->id);
                       $timesheetEntryProject = new TimesheetEntryProject();
                       $timesheetEntryProject->timesheet_entry_id = $timesheetEntry->id;
                       $timesheetEntryProject->project_id = $project->id;
                       $timesheetEntryProject->work_duration = 0;
                       $timesheetEntryProject->save();
                   }else{
                       // log project already exist
                       $this->info('project already exist '.$timesheetEntry->id.' and project : '.$project->id);
                   }
               }
           }else{

           }
        }
      }
      return true;
    }
    

}