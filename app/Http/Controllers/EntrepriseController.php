<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\EntrepriseResource; 
use App\Models\Entreprise;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
 
class EntrepriseController extends Controller
{

    public $customMessages = [
        'required' => 'Le champ :attribute est requis.',
        'string' => 'Le champ :attribute doit être une chaîne de caractères.',
        'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
        'email' => 'Le champ :attribute doit être une adresse email valide.',
        'unique' => 'Le  champ :attribute est déjà utilisée.',
        'date' => 'Le champ :attribute doit être une date valide.',
        'min' => 'Le champ :attribute doit contenir au moins :min caractères.',
        'in' => 'Le champ :attribute doit être l\'une des valeurs suivantes : :values.',
    ];

//    crud for entreprises

    public function index(Request $request)
    {
        // $entreprises = Entreprise::paginate(10);
        // get entreprises ordered by id desc
        $entreprises = Entreprise::orderBy('name', 'asc')->paginate(10);
        return EntrepriseResource::collection($entreprises);
    }

    // store
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:entreprises,domain',
            'linkedin' => 'string|max:255',
            'adress' => 'string|max:255',
            'telephone' => 'string|max:255',
            'email' => 'string|max:255',
            'website' => 'string|max:255',
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        $entreprise = Entreprise::create([
            'name' => $request['name'],
            'domain' => $request['domain'],
            'linkedin' => $request['linkedin'],
            'adress' => $request['adress'],
            'telephone' => $request['telephone'],
            'email' => $request['email'],
            'website' => $request['website'],
        ]);

        return response()->json(array('message' => __("Entreprise ajoutée"), 'data' => new EntrepriseResource($entreprise)), 200);
    }

    // update
    public function update(Request $request, $id)
    {
        $entreprise = Entreprise::find($id);

        if(!$entreprise){
            return response()->json(array('message' => __("Entreprise introuvable")), 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'adress' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        $entreprise->update([
            'name' => $request['name'],
            'domain' => $request['domain'],
            'linkedin' => $request['linkedin'],
            'adress' => $request['adress'],
            'telephone' => $request['telephone'],
            'email' => $request['email'],
            'website' => $request['website'],
        ]);

        return response()->json(array('message' => __("Entreprise modifiée"), 'data' => new EntrepriseResource($entreprise)), 200);
    }

    // delete
    public function destroy($id)
    {
        $entreprise = Entreprise::find($id);
        if(!$entreprise){
            return response()->json(array('message' => __("Entreprise introuvable")), 404);
        }

        $entreprise->delete();
        return response()->json(array('message' => __("Entreprise supprimée")), 200);
    }

    public function export()
    {
        set_time_limit(30000);
        // Récupérer les données de la table entreprises
        $entreprises = Entreprise::all();

        // get excel file empty.xlsx and copy it to the new file
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('empty_entreprise.xlsx');

        $sheet = $spreadsheet->getActiveSheet();
      

        // Ajouter les données à la feuille
        $row = 2;
        foreach ($entreprises as $entreprise) {

            $sheet->setCellValue('A' . $row, $entreprise->name ?? ''); 
            $sheet->setCellValue('B' . $row, $entreprise->domain ?? '');
            $sheet->setCellValue('C' . $row, $entreprise->linkedin ?? '');
            $sheet->setCellValue('D' . $row, $entreprise->adress ?? '');
            $sheet->setCellValue('E' . $row, $entreprise->telephone ?? '');
            $sheet->setCellValue('F' . $row, $entreprise->email ?? '');
            $sheet->setCellValue('G' . $row, $entreprise->website ?? '');

            $row++;
        } 

        // Créer un writer pour écrire le fichier
        $writer = new Xlsx($spreadsheet);
        $fileName = 'entreprises_' . date('Y-m-d_H-i-s') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        // Déplacer le fichier temporaire vers le dossier public
        $url = '/' . $fileName;
        rename($tempFile, public_path($url));
        $url =  env('APP_URL').':8000'.$url;

        // Supprimer le fichier temporaire
        // unlink($tempFile);

        return response()->json(['message' => 'Export effectué','url' => $url], 200);
    }

    // search by name,domaine,linkedin
    public function search(Request $request)
    {
        $search = $request->search;
        $entreprises = Entreprise::where('name', 'like', '%'.$search.'%')
            ->orWhere('domain', 'like', '%'.$search.'%')
            ->orWhere('linkedin', 'like', '%'.$search.'%')
            ->paginate(10);
        return EntrepriseResource::collection($entreprises);
    }
    
    

    public function checkEntreprise($domain)
    {
        $entreprise = Entreprise::where('domain', $domain)->first();
        if($entreprise){
            return response()->json(array('message' => __("Entreprise existe"), 'data' => new EntrepriseResource($entreprise)), 200);
        }
        return response()->json(array('message' => __("Entreprise n'existe pas"), 'data' => null), 404);
    }

    public function syncEntreprise(Request $request)
    {
        $entreprise = Entreprise::where('name', $request->name)->first();
        if(!$entreprise){
            // create entreprise
            $entreprise = Entreprise::create([
                'name' => $request['name'],
                'domain' => $request['domain'],
                'linkedin' => $request['linkedin'],
                'size' => $request['size'],
                'industry' => $request['industry'],
                'adress' => $request['adress'],
                'telephone' => $request['telephone'],
                'email' => $request['email'],
                'website' => $request['website'],
            ]);

            // if logo exists
            if($request->logo){
                $entreprise->addMediaFromUrl($request->logo)->toMediaCollection('logo');
            }
            $entreprise->save();


            return response()->json(array('message' => __("Entreprise ajoutée"), 'data' => new EntrepriseResource($entreprise)), 200);
        }else{
            // update entreprise
            $entreprise->update([
                'linkedin' => $request['linkedin'],
                'size' => $request['size'],
                'industry' => $request['industry'],
                'adress' => $request['adress'],
                'telephone' => $request['telephone'],
                'email' => $request['email'],
                'website' => $request['website'],
                'domain' => $request['domain'],
            ]);
            // if logo exists
            if($request->logo){
                $entreprise->addMediaFromUrl($request->logo)->toMediaCollection('logo');
            }
            $entreprise->save();



            return response()->json(array('message' => __("Entreprise modifiée"), 'data' => new EntrepriseResource($entreprise)), 200);
        }
    }


    

}