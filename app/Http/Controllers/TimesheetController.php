<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\TimesheetResource; 
use App\Http\Resources\TimesheetFullResource; 
use App\Http\Resources\MonthYearResource; 
use App\Models\Timesheet;
use App\Models\TimesheetEntry;
use App\Models\TimesheetEntryProject;
use App\Models\Project;
use App\Models\Entreprise;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
// Log laravel
use Illuminate\Support\Facades\Log;

 
class TimesheetController extends Controller
{

    public $customMessages = [
        'required' => 'Le champ :attribute est requis.',
        'string' => 'Le champ :attribute doit être une chaîne de caractères.',
        'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
        'email' => 'Le champ :attribute doit être une adresse email valide.',
        'unique' => 'Le  champ :attribute est déjà utilisé.',
        'date' => 'Le champ :attribute doit être une date valide.',
        'min' => 'Le champ :attribute doit contenir au moins :min caractères.',
        'in' => 'Le champ :attribute doit être l\'une des valeurs suivantes : :values.',
    ];
    //   crud for timesheets
    public function index(Request $request)
    {
        // get timesheets order by id desc paginated 10 where user_id = auth user id
        $timesheets = Timesheet::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->paginate(10);
        return TimesheetResource::collection($timesheets);
    }

    // get all month and year
    public function getMonthYear(Request $request)
    {
        // get all month and year distinct order by year and month desc
        $monthYear = DB::table('timesheets')
            ->select('year', 'month', DB::raw('count(id) as total'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return MonthYearResource::collection($monthYear);
        
    }

    // get all timesheets by month and year
    public function getTimesheetByMonth()
    {
        // get year and month from url
        $year = request()->input('year');
        $month = request()->input('month');
        // get all timesheets for the month and year
        $timesheets = Timesheet::where('year', $year)
            ->where('month', $month)
            ->orderBy('id', 'desc')
            ->get();
        return TimesheetResource::collection($timesheets);
    }

    // admin get all timesheets
    public function all(Request $request)
    {
        // with status pending_validation
        $timesheets = Timesheet::orderBy('id', 'desc')->where('status', 'pending_validation')->paginate(10);
        return TimesheetResource::collection($timesheets);
    }

    // get timesheet by id 
    public function getTimesheet($id)
    {
        $timesheet = Timesheet::find($id);

        if (!$timesheet) {
            return response()->json(['message' => 'Timesheet introuvable'], Response::HTTP_NOT_FOUND);
        }

        $this->update_timesheet($timesheet);
        
        $timesheet = Timesheet::find($id);

        return new TimesheetFullResource($timesheet);
    }

     // update the timesheet
     public function update_timesheet($timesheet)
     {
        //laravel log 
        Log::info('update_timesheet');

       $timesheet_id = $timesheet->id;
       $user_id = $timesheet->user_id;
       $timesheet = Timesheet::find($timesheet_id);
       $month = $timesheet->month;
       $year = $timesheet->year;
       $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
 
       if($timesheet){
        $projects = $timesheet->user->projects;
        $entries = $timesheet->timesheetEntries;

        foreach($entries as $entry){

            foreach($projects as $project){
                // get entry project where timesheet entry id $timesheetEntry->id and project id $project->id
                $timesheetEntryProject = TimesheetEntryProject::where('timesheet_entry_id', $entry->id)->where('project_id', $project->id)->first();
                if(!$timesheetEntryProject){
                    // log project not exist
                    Log::info('project not exist '.$entry->id.' and project : '.$project->id);
                    $timesheetEntryProject = new TimesheetEntryProject();
                    $timesheetEntryProject->timesheet_entry_id = $entry->id;
                    $timesheetEntryProject->project_id = $project->id;
                    $timesheetEntryProject->work_duration = 0;
                    $timesheetEntryProject->save();
                }else{
                    // log project already exist
                    Log::info('project already exist '.$entry->id.' and project : '.$project->id);
                }
            }
        } 
       }
       return true;
    }
 

    // store
    public function store(Request $request)
    {
        // timesheets(id,user_id,project_id,status,comment,month,year)
        $validator = Validator::make($request->all(), [
            'month' => 'required|string|max:255',
            'year' => 'required|string|max:255',
        ], $this->customMessages);
        
        $errors = $validator->errors();
        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        // check if another timesheet exists for the same project, month and year
        $timesheet = Timesheet::where('month', $request['month'])
            ->where('year', $request['year'])
            ->where('user_id', Auth::user()->id)
            ->first();

        if($timesheet){
            return response()->json(array('message' => __("Vous ne pouvez pas ajouter un timesheet pour le même mois et année"), 'data' => $timesheet), 422);
        }

        $user = Auth::user();

        $timesheet = Timesheet::create([
            'month' => $request['month'],
            'year' => $request['year'],
            'user_id' => $user->id,
        ]);

        $month = $timesheet->month;
        $year = $timesheet->year;

        // create timesheet entries for the timesheet with the same month and year for all days of the month
        $timesheetEntries = [];
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        // all pending project
        // $projects = Project::where('status', 'pending')->get();

        $projects = $user->projects;
         
        for($i = 1; $i <= $daysInMonth; $i++){
            $timesheetEntry = new TimesheetEntry();
            $timesheetEntry->day = $i;
            $timesheetEntry->date = $year.'-'.$month.'-'.$i;
            $timesheetEntry->timesheet_id = $timesheet->id;
            $timesheetEntry->work_duration = 0;
            $timesheetEntry->save();

            foreach($projects as $project){
                $timesheetEntryProject = new TimesheetEntryProject();
                $timesheetEntryProject->timesheet_entry_id = $timesheetEntry->id;
                $timesheetEntryProject->project_id = $project->id;
                $timesheetEntryProject->work_duration = 0;
                $timesheetEntryProject->save();
            }

            $timesheetEntries[] = $timesheetEntry;
        }



        $timesheet->save();
        return response()->json(array('message' => __("Timesheet ajouté"), 'data' => new TimesheetResource($timesheet)), 200);
    }

    // update
    public function update(Request $request, $id)
    {
        $timesheet = Timesheet::find($id);

        if(!$timesheet){
            return response()->json(array('message' => __("Timesheet introuvable")), 404);
        }

        $validator = Validator::make($request->all(), [
            'comment' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255', // 'pending', 'approved', 'rejected'
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        $timesheet = Timesheet::where('id', $id)->first();

        // if status is approved  , set comment to null
        if($request['status'] == 'approved'){
            $request['comment'] = null;
        }

        $timesheet->fill($request->all());

        $timesheet->save();

        return response()->json(array('message' => __("Timesheet modifié"), 'data' => new TimesheetResource($timesheet)), 200);
    }

    // delete
    public function destroy($id)
    {
        $timesheet = Timesheet::find($id);
        if(!$timesheet){
            return response()->json(array('message' => __("Timesheet introuvable")), 404);
        }
        $timesheet->delete();
        return response()->json(array('message' => __("Timesheet supprimé")), 200);
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $entreprises = Timesheet::where('title', 'like', '%'.$search.'%')
            ->orWhere('client', 'like', '%'.$search.'%')
            ->paginate(10);
        return TimesheetResource::collection($entreprises);
    }

    // update entry by id   
    public function updateEntry(Request $request, $id)
    {
        $timesheetEntry = TimesheetEntry::find($id);

        if(!$timesheetEntry){
            return response()->json(array('message' => __("Entrée de timesheet introuvable")), 404);
        }

        $validator = Validator::make($request->all(), [
            // 'work_duration' => 'required|double|max:255',
            'work_duration' => ['required',Rule::in([0, 0.25, 0.5, 0.75, 1])],
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        $timesheetEntry->fill($request->all());

        $timesheet = $timesheetEntry->timesheet;
        $timesheet->status = 'pending';
        $timesheet->save();

        $timesheetEntry->save();

        return response()->json(array('message' => __("Mise à jour effectué")), 200);
    }

    public function updateEntryProject(Request $request, $id)
    {
        $timesheetEntryProject = TimesheetEntryProject::find($id);

        if(!$timesheetEntryProject){
            return response()->json(array('message' => __("Entrée de timesheet introuvable")), 404);
        }

        $validator = Validator::make($request->all(), [
            // 'work_duration' => 'required|double|max:255',
            'work_duration' => ['required',Rule::in([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16])],
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        $timesheetEntryProject->fill($request->all());

        $timesheetEntryProject->save();

        return response()->json(array('message' => __("Mise à jour effectué")), 200);
    }

    public function export()
    {
        set_time_limit(30000);

        $year = request()->input('year');
        $month = request()->input('month');
        // get all timesheets for the month and year
        $timesheets = Timesheet::where('year', $year)
            ->where('month', $month)
            ->orderBy('id', 'desc')
            ->get();

        // get excel file empty.xlsx and copy it to the new file
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('empty_timesheet.xlsx');

        $sheet = $spreadsheet->getActiveSheet();

        // Ajouter les donnés à la feuille
        $row = 3;
        $timesheets = TimesheetResource::collection($timesheets);
        if($timesheets->count() == 0){
            return response()->json(['message' => 'Aucun tempsheet à exporter'], 200);
        }

        // month_label
        $sheet->setCellValue('C1', $timesheets[0]->getMonthLabel().' '.$timesheets[0]->year ?? '');
        // set font size of month and year
        $sheet->getStyle('C1')->getFont()->setSize(30);
        $sheet->getStyle('C1')->getAlignment()->setHorizontal('center');

        foreach ($timesheets as $timesheet) {
            
            $sheet->setCellValue('A' . $row, $timesheet->user->first_name ?? ''); 
            $sheet->setCellValue('B' . $row, $timesheet->user->last_name ?? '');
            $sheet->setCellValue('C' . $row, $timesheet->total_work_duration_day ?? ''); 
            $sheet->setCellValue('D' . $row, $timesheet->user->tjm ?? ''); 
            $sheet->setCellValue('E' . $row, ($timesheet->user->tjm * $timesheet->total_work_duration_day) ?? '');
            $sheet->setCellValue('F' . $row, $timesheet->getStatusLabel() ?? ''); 
            $row++;
        } 

        // Crér un writer pour écrire le fichier with rigths 644

        $writer = new Xlsx($spreadsheet);
        $fileName = 'timesheets_' . date('Y-m-d_H-i-s') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        chmod($tempFile, 0644);
        $writer->save($tempFile);

        // Déplacer le fichier temporaire vers le dossier public
        $url = '/' . $fileName;
        rename($tempFile, public_path($url));
        $url =  env('APP_URL') .$url;

        return response()->json(['message' => 'Export effectué','url' => $url], 200);
    }

    public function reinit($id){
        // reinit all timesheet entry
        $timesheetEntry = TimesheetEntry::where('timesheet_id', $id)->get();  
          
        foreach($timesheetEntry as $entry){
            $entry->work_duration = 0;
            // reinit the project
            $timesheetEntryProjects = TimesheetEntryProject::where('timesheet_entry_id', $entry->id)->get();
            foreach($timesheetEntryProjects as $timesheetEntryProject){
                $timesheetEntryProject->work_duration = 0;
                $timesheetEntryProject->save();
            }
            $entry->save();
        }

        return response()->json(array('message' => __("Reinitialisation effectuée")), 200);

    }
            

}