<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource; 
use App\Models\Project;
use App\Models\UserProject;
use App\Models\Entreprise;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
 
class ProjectController extends Controller
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
    //   crud for projects
    public function index(Request $request)
    {
        // get projects order by id desc paginated 10
        $projects = Project::orderBy('id', 'desc')->paginate(10);
        return ProjectResource::collection($projects);
    }

    // get project with status pending
    public function pending(Request $request)
    {
        // get projects order by id desc paginated 10

        // $projects = Project::where('status', 'pending')->orderBy('id', 'desc')->paginate(10);

        // get user projects
        $userProjects = UserProject::where('user_id', Auth::user()->id)->get();
        $projects = Project::whereIn('id', $userProjects->pluck('project_id'))->where('status', 'pending')->orderBy('id', 'desc')->paginate(10);

        return ProjectResource::collection($projects);
    }

    // store
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'client' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ], $this->customMessages);
        
        $errors = $validator->errors();
        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        $project = Project::create([
            'title' => $request['title'],
            'description' => $request['description'],
            'client' => $request['client'],
            'status' => $request['status'],
        ]);
        
        $project->save();
        return response()->json(array('message' => __("Project ajouté"), 'data' => new ProjectResource($project)), 200);
    }

    // update
    public function update(Request $request, $id)
    {
        $project = Project::find($id);

        if(!$project){
            return response()->json(array('message' => __("Project introuvable")), 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'client' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'users' => 'nullable|string',
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        
        $project->fill($request->all());
        // exemple users = 1,2,3
        $users = $request->users;
        $users = explode(',', $users);
        // delete all users from project
        UserProject::where('project_id', $project->id)->delete();
        if($users){
            // add users to project
            foreach ($users as $user) {
                UserProject::create([
                    'project_id' => $project->id,
                    'user_id' => $user,
                ]);
            }
        }

        $project->save();

        return response()->json(array('message' => __("Project modifié"), 'data' => new ProjectResource($project)), 200);
    }

    // delete
    public function destroy($id)
    {
        $project = Project::find($id);
        if(!$project){
            return response()->json(array('message' => __("Project introuvable")), 404);
        }
        $project->delete();
        return response()->json(array('message' => __("Project supprimé")), 200);
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $entreprises = Project::where('title', 'like', '%'.$search.'%')
            ->orWhere('client', 'like', '%'.$search.'%')
            ->paginate(10);
        return ProjectResource::collection($entreprises);
    }


}