<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Lesson;
// use Owenoj\LaravelGetId3\GetId3;
use getID3;


class UpdateLessonDurations extends Command
{
    protected $signature = 'lessons:update-durations';
    protected $description = 'Update durations of lessons with null duration';

    public function handle()
    {
        // $lessons = Lesson::whereNull('duration')->get();
        $lessons = Lesson::all();
        $this->info("Start updating duration for Lessons");
        foreach ($lessons as $lesson) {
            
            $media = $lesson->getFirstMediaUrl('video');
            $this->info("Lesson ".$lesson->id." video : ".$media);
            // $media = "https://api.themusichall.fr/assets/28/default_video.mp4";
            
            if ($media) {
                $duration = $this->getVideoDuration($media);

                if ($duration) {
                    $lesson->update(['duration' => $duration]);
                    $this->info("Updated duration for Lesson {$lesson->id}");
                }
            }
        }
        $this->info("Finish updating duration for Lessons");

    }

    protected function getVideoDuration($path)
    {
        // Copy remote file locally to scan with getID3()
        $duration = null;
        if ($fp_remote = fopen($path, 'rb')) {
            $localtempfilename = tempnam('/tmp', 'getID3');
            if ($fp_local = fopen($localtempfilename, 'wb')) {
                while ($buffer = fread($fp_remote, 8192)) {
                    fwrite($fp_local, $buffer);
                }
                fclose($fp_local);
                $getID3 = new getID3;
                $file = $getID3->analyze($localtempfilename);
                $duration = intval($file['playtime_seconds']);
                $this->info("Get duration ".$duration." for the Lesson");
                unlink($localtempfilename);
            }
            fclose($fp_remote);
        }
        return $duration;
         
    }
}