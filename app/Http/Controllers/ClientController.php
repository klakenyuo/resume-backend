<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource; 
use App\Models\Client;
use App\Models\Entreprise;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
 
class ClientController extends Controller
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

//    crud for clients

    public function index(Request $request)
    {
        // get clients order by id desc paginated 10
        $clients = Client::orderBy('id', 'desc')->paginate(10);
        return ClientResource::collection($clients);
    }

    // store
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // name,website,domain,country,city,adress,contact,telephone,email,logo
            'name' => 'required|string|max:255',
            'website' => 'nullable|string|max:255',
            'domain' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'adress' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        $client = Client::create([
            'name' => $request['name'],
            'website' => $request['website'],
            'domain' => $request['domain'],
            'country' => $request['country'],
            'city' => $request['city'],
            'adress' => $request['adress'],
            'contact' => $request['contact'],
            'telephone' => $request['telephone'],
            'email' => $request['email'],
        ]);

        // if logo add logo to media 
        if($request->hasFile('logo')){
            $client->addMedia($request->file('logo'))->toMediaCollection('logo');
        }

        $client->save();


        return response()->json(array('message' => __("Client ajouté"), 'data' => new ClientResource($client)), 200);
    }

    // update
    public function update(Request $request, $id)
    {
        $client = Client::find($id);

        if(!$client){
            return response()->json(array('message' => __("Client introuvable")), 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'website' => 'nullable|string|max:255',
            'domain' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'adress' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

            //    fill the request data
        $client->fill($request->all());

        // if logo add logo to media
        if($request->hasFile('logo')){
            $client->addMedia($request->file('logo'))->toMediaCollection('logo');
        }
        $client->save();

        return response()->json(array('message' => __("Client modifié"), 'data' => new ClientResource($client)), 200);
    }

    // delete
    public function destroy($id)
    {
        $client = Client::find($id);
        if(!$client){
            return response()->json(array('message' => __("Client introuvable")), 404);
        }

        $client->delete();

        return response()->json(array('message' => __("Client supprimé")), 200);
    }

    public function export()
    {
        set_time_limit(30000);
        // Récupérer les donnés de la table clients
        $clients = Client::all();

        // get excel file empty.xlsx and copy it to the new file
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('empty_client.xlsx');

        $sheet = $spreadsheet->getActiveSheet();
      

        // Ajouter les donnés à la feuille
        $row = 2;
        $clients = ClientResource::collection($clients);
        foreach ($clients as $client) {
            
            $sheet->setCellValue('A' . $row, $client->first_name ?? ''); 
            $sheet->setCellValue('B' . $row, $client->last_name ?? '');
            $sheet->setCellValue('C' . $row, $client->email ?? '');
            $sheet->setCellValue('D' . $row, $client->telephone ?? '');
            $sheet->setCellValue('E' . $row, $client->adress ?? '');
            $sheet->setCellValue('F' . $row, $client->entreprise ?? '');
            $sheet->setCellValue('G' . $row, $client->linkedin ?? '');

            $row++;
        } 

        // Crér un writer pour écrire le fichier
        $writer = new Xlsx($spreadsheet);
        $fileName = 'clients_' . date('Y-m-d_H-i-s') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        // Déplacer le fichier temporaire vers le dossier public
        $url = '/' . $fileName;
        rename($tempFile, public_path($url));
        $url =  env('APP_URL').':8000'.$url;
        return response()->json(['message' => 'Export effectué','url' => $url], 200);
    }


    public function search(Request $request)
    {
        $search = $request->search;
        $entreprises = Client::where('name', 'like', '%'.$search.'%')
            ->orWhere('contact', 'like', '%'.$search.'%')
            ->orWhere('telephone', 'like', '%'.$search.'%')
            ->orWhere('adress', 'like', '%'.$search.'%')
            ->orWhere('email', 'like', '%'.$search.'%')
            ->paginate(10);
        return ClientResource::collection($entreprises);
    }


}