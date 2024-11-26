<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProfilResource; 
use App\Http\Resources\ProfilTagResource; 
use App\Models\Profil;
use App\Models\ProfilTag;
use App\Models\Entreprise;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Http;
use \Spatie\Tags\Tag;

// log facade
use Illuminate\Support\Facades\Log; 



 
class ProfilController extends Controller
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

//    crud for profils

    public function index(Request $request)
    {
        // get profils order by id desc paginated 10
        $profils = Profil::orderBy('id', 'desc')->paginate(10);

        // foreach ($profils as $profil) {
        //     $tags = $profil->tags_labels;
        //     $profil->syncTagsWithType($tags, 'profil');
        // }

        return ProfilResource::collection($profils);
    }

    // store
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'entreprise_id' => 'nullable|integer',
            'title' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'adress' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'about' => 'nullable|string',
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        $profil = Profil::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
            'entreprise_id' => $request['entreprise_id'],
            'title' => $request['title'],
            'linkedin' => $request['linkedin'],
            'telephone' => $request['telephone'],
            'email' => $request['email'],
            'website' => $request['website'],
            'adress' => $request['adress'],
            'city' => $request['city'],
            'country' => $request['country'],
            'postal_code' => $request['postal_code'],
            'about' => $request['about'],
        ]);

        return response()->json(array('message' => __("Profil ajouté"), 'data' => new ProfilResource($profil)), 200);
    }

    // update
    public function update(Request $request, $id)
    {
        $profil = Profil::find($id);

        if(!$profil){
            return response()->json(array('message' => __("Profil introuvable")), 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'entreprise_id' => 'nullable|integer',
            'title' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'adress' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'about' => 'nullable|string',
            'tags' => 'nullable|string',
             
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        
        //    fill the request data
        $profil->fill($validator->validated());

        $profil->save();

        return response()->json(array('message' => __("Profil modifié"), 'data' => new ProfilResource($profil)), 200);
    }

    // delete
    public function destroy($id)
    {
        $profil = Profil::find($id);
        if(!$profil){
            return response()->json(array('message' => __("Profil introuvable")), 404);
        }

        $profil->delete();

        return response()->json(array('message' => __("Profil supprimé")), 200);
    }

    public function export()
    {
        set_time_limit(30000);
        // Récupérer les donnés de la table profils
        $profils = Profil::all();

        // get excel file empty.xlsx and copy it to the new file
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('empty_profil.xlsx');

        $sheet = $spreadsheet->getActiveSheet();
      

        // Ajouter les donnés à la feuille
        $row = 2;
        $profils = ProfilResource::collection($profils);
        foreach ($profils as $profil) {
            $sheet->setCellValue('A' . $row, $profil->first_name ?? ''); 
            $sheet->setCellValue('B' . $row, $profil->last_name ?? '');
            $sheet->setCellValue('C' . $row, $profil->email ?? '');
            $sheet->setCellValue('D' . $row, $profil->telephone ?? '');
            $sheet->setCellValue('E' . $row, $profil->adress ?? '');
            $sheet->setCellValue('F' . $row, $profil->entreprise ?? '');
            $sheet->setCellValue('G' . $row, $profil->linkedin ?? '');

            $row++;
        } 

        // Crér un writer pour écrire le fichier
        $writer = new Xlsx($spreadsheet);
        $fileName = 'profils_' . date('Y-m-d_H-i-s') . '.xlsx';
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


    public function search(Request $request)
    {
        $search = $request->search;

        $tags = $request->tags;

      
       // Requête de base pour la recherche de profils
        $profils = Profil::query();

        // Vérifie si une recherche a été effectuée
        if ($search) {
            $profils->where(function($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('telephone', 'like', '%' . $search . '%')
                    ->orWhere('adress', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('linkedin', 'like', '%' . $search . '%');
            });
        }

        // Vérifie si des tags ont été fournis
        if ($tags && is_array($tags)) {
            $profils->where(function($query) use ($tags) {
                foreach ($tags as $tag) {
                    $query->orWhere('tags', 'like', '%' . $tag . '%');
                }
            });
        }

        // Pagination des résultats
        $profils = $profils->paginate(10);

        // Retourne la collection de profils paginée
        return ProfilResource::collection($profils);
    }

    // check if profil exists by name
    public function checkProfil(Request $request)
    {
        // validate the request
        $validator = Validator::make($request->all(), [
            'profile_id' => 'required|string|max:255',
        ], $this->customMessages);

        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        $profile_id = $request->profile_id;

        $profil = Profil::where('profile_id', $profile_id)->first();

        if($profil){
            return response()->json(array('code' => __("exist")), 200);
        }else{
            return response()->json(array('code' => __("dont_exist")), 404);
        }

    }

    // sync profils
    public function syncProfils(Request $request)
    {
        

        // return $this->hunt('Alexis','Ohanian','reddit.com');

        $profils = $request->data;
        // return $profils;
        $total_profils = count($profils);

        $scraped_profils = 0;

        foreach ($profils as $profil) {
            $name = $profil['name'];
            if($name == "" || $name == null || $name == " " || $name == "null"){
                $total_profils--;
                continue;
            }

            $exist = Profil::whereRaw('LOWER(CONCAT(first_name, " ", last_name)) = ?', [strtolower($name)])->first();
            if(!$exist){
                $name = explode(' ', $name);
                $first_name = $name[0];
                $name = implode(' ', $name);
                // last name is the rest of all substrings of the name without the first one
                $last_name = substr($name, strlen($first_name) + 1);
    
                $headline = $profil['headline'] ?? '';
                $company = $profil['company'] ?? '';
                $location = $profil['location'] ?? '';
                $linkedinUrl = $profil['linkedinUrl'] ?? '';
    
                // $data_scraped = $this->hunt($first_name,$last_name,$company);
                $email = $data_scraped->email ?? " ";
                $domain = $data_scraped->domain ?? " ";
    
                $entreprise_id = $this->get_entreprise_id($company,$domain);
                $contact = Profil::create([
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email'=> $data_scraped->email ?? '',
                    'entreprise_id' => $entreprise_id,
                    'title' => $headline,
                    'linkedin' => $linkedinUrl,
                ]);

                //  if photo
                if($contact){
                    if($profil['photo']){
                        $photo = $profil['photo'];
                        $contact->addMediaFromUrl($photo)->toMediaCollection('photo');
                    }
                    $contact->save();
                }

                $scraped_profils++;
            }else{
                // update profil

                $headline = $profil['headline'] ?? '';
                $linkedinUrl = $profil['linkedinUrl'] ?? '';
                $contact = Profil::find($exist->id);

                $exist->update([
                    'title' => $headline,
                    'linkedin' => $linkedinUrl,
                ]);
                if($profil['photo']){
                    $photo = $profil['photo'];
                    $contact->addMediaFromUrl($photo)->toMediaCollection('photo');
                }
                $contact->save();
                
            }
        }

        return response()->json(array('message' => __("Profils synchronisés"),
            'total_profils' => $total_profils,
            'scraped_profils' => $scraped_profils,
            'new_profils' => $scraped_profils
    ), 200);
    }


     // sync profils
     public function addProfil(Request $request)
     {
         
        $profil = $request->data;
        $keywords = $request->keywords ?? '';


        $name = $profil['name'];
        $profil_id = $profil['profileId'];



        $exist = Profil::where('profile_id', $profil_id)->first();
        // $exist = Profil::whereRaw('LOWER(CONCAT(first_name, " ", last_name)) = ?', [strtolower($name)])->where('profile_id', $profil_id)->first();
        
        if(!$exist){
            $name = explode(' ', $name);
            $first_name = $name[0];
            $name = implode(' ', $name);

            $last_name = substr($name, strlen($first_name) + 1);

            $tag = ProfilTag::where('name', $keywords)->first();

            if($tag){
                $tags = $tag->name;
            }else{
                $tag = ProfilTag::create([
                    'name' => $keywords,
                    'color' => $this->get_random_color(),
                ]);
                $tags = $tag->name;
            }

            var_dump($tags);

            $headline = $profil['headline'] ?? '';
            $company = $profil['company'] ?? '';
            $location = $profil['location'] ?? '';
            $linkedinUrl = $profil['linkedinUrl'] ?? '';

            $contact = Profil::create([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email'=> $data_scraped->email ?? '',
                'title' => $headline,
                'linkedin' => $linkedinUrl,
                'profile_id' => $profil_id,
                // 'tags' => '["'.$tags.'"]',
            ]);

            //  if photo
            if($contact){
                if($profil['photo']){
                    $photo = $profil['photo'];
                    $contact->addMediaFromUrl($photo)->toMediaCollection('photo');
                }
                $contact->save();
            }

            return response()->json(array('message' => __("Profil synchronisé")), 200);
        } 

        return response()->json(array('message' => __("Profil non synchronisé")), 422);

     }

    public function get_entreprise_id($name,$domain,$website='')
    {
        $entreprise = Entreprise::where('name', $name)->first();
        if($entreprise){
            return $entreprise->id;
        }else{
            $entreprise = Entreprise::create([
                'name' => $name,
                'domain' => $domain,
                'website' => $website,
            ]);
            return $entreprise->id;
        }
    }

    // function to scape data of user by first name and last name and domain of entreprise
    public function hunt($first_name,$last_name,$company){
        // https://api.hunter.io/v2/email-finder?domain=reddit.com&first_name=Alexis&last_name=Ohanian&api_key=a8928e8f245034ade562d6ce20038d0db6e83a62
        // trim first name and last name and company
        // dd($first_name,$last_name,$company);
        // get company domain
        $url = 'https://api.hunter.io/v2/email-finder?domain='.$company.'&first_name='.$first_name.'&last_name='.$last_name.'&api_key=a8928e8f245034ade562d6ce20038d0db6e83a62';
        $response = file_get_contents($url);
        $response = json_decode($response);
        if($response->data->score && $response->data->score > 70){
            return $response->data;
        }
        return null;
    }

    public function verify($email){

        $url = 'https://api.hunter.io/v2/email-verifier?email='.$email.'&api_key=a8928e8f245034ade562d6ce20038d0db6e83a62';
        
        $response = file_get_contents($url);
        $response = json_decode($response);

        if($response->data->score && $response->data->score > 70){
            return $response->data;
        }

        return null;

    }


    // function to get total profils count who have entreprise_id ant theire entreprise domain is not null
    public function getProfilsCount()
    {
        //  get entreprise that have domain not null or empty
        $entreprises = Entreprise::whereNotNull('domain')->get();
        $total_profils = 0;
        foreach ($entreprises as $entreprise) {
            if($entreprise->domain == "" || $entreprise->domain == null){
                continue;
            }else{
                $profils = Profil::where('entreprise_id', $entreprise->id)->whereNull('email')->get();
                $total_profils += count($profils);
            }
        }
        return response()->json(array('total_profils' => $total_profils,'total_entreprises'=>count($entreprises)), 200);
    }

    // enrich with contactout
    public function enrichProfilWithContactOut($id){
        $profil = Profil::find($id);

        if (!$profil) {
            return response()->json(array('message' => __("Profil introuvable")), 404);
        }
        $linkedinUrl = $profil->linkedin;

        $data = $this->callContactApi($linkedinUrl);

        if(!$data){
            return response()->json(array('message' => __("Profil non enrichi") ,'data'=>$data), 502);
        } 

        $profile_data = $data['profile'];

        $phone = isset($profile_data['phones']) && count($profile_data['phones']) >0 ? $profile_data['phones'][0] : '';
        $email = isset($profile_data['email']) && count($profile_data['email']) >0 ? $profile_data['email'][0] : '';

        if($profil->email){
            $profil->email_two = $email;
        }else{
            $profil->email = $email;
        }

        if($profil->telephone){
            $profil->telephone_two = $phone;
        }else{
            $profil->telephone = $phone;
        }

        $profil->enrich_status = 'enriched';
        $profil->is_enrich_cout = true;
        $profil->cout_data = json_encode($data['profile']);
        $profil->save();

        return response()->json(array('message' => __("Profil enrichi"),'data'=>$profile_data), 200);


    }

    // enrichProfil id
    public function enrichProfil($id){
        $profil = Profil::find($id);
        if (!$profil) {
            return response()->json(array('message' => __("Profil introuvable")), 404);
        }

        $name = $profil->first_name.' '.$profil->last_name;
        $profile_id = $profil->profile_id;

        $can_enrich = $profil->can_enrich;

        if($can_enrich){
            $data = $this->callKasprApi($name,$profile_id);

            if(!$data){
                // log
                Log::error('Profil enrichi - kaspr no data found');
                return response()->json(array('message' => __("Profil non enrichi") ,'data'=>$data), 502);
            } 

            $data = json_decode($data, true);

            $profile_data = $data['profile'];
            $phone = '';
            if(!empty($profile_data['phones'])){
                $phone = $profile_data['starryPhone'] ?? '';
            }

            $phone_two = isset($profile_data['phones']) && count($profile_data['phones']) >1 ? $profile_data['phones'][1] : '';

            $email = $profile_data['starryWorkEmail'] ?? '';
            $email_two = isset($profile_data['professionalEmails']) && count($profile_data['professionalEmails']) >1 ? $profile_data['professionalEmails'][1] : '';

            $company = $profile_data['company'] ?? '';

            $domain = $company['domains'][0] ?? '';
            $company_name = $company['name'] ?? '';
            $website = $company['companyPageUrl'] ?? '';

            $entreprise_id = $this->get_entreprise_id($company_name,$domain,$website);
            if($entreprise_id){
                $profil->entreprise_id = $entreprise_id;
            }
            $profil->email = $email;
            $profil->email_two = $email_two;
            $profil->telephone = $phone;
            $profil->telephone_two = $phone_two;
            $profil->enrich_status = 'enriched';
            $profil->kaspr_data = json_encode($data['profile']);
            $profil->save();
            return response()->json(array('message' => __("Profil enrichi"),'data'=>$profile_data), 200);

        }

        return response()->json(array('message' => __("Profil non enrichi")), 502);

    
    }

    public function callContactApi($linkedinUrl)
    {
        // URL de l'API
        $url = "https://api.contactout.com/v1/people/linkedin?include_phone=true&email_type=work&profile=".$linkedinUrl;

        // Récupérer le bearer token depuis le fichier .env
        $token = 'C2QrWaFyJQ86Dx0XlhBqmoyW';

        // Effectuer la requête POST avec le bearer token
        $response = Http::withHeaders([
                        'token' => $token,
                    ])
                    ->get($url);

        // Vérifier si la requête a réussi
        if ($response->successful()) {
            // Retourner les données de la réponse
            return $response->json();
        } else {
            // response message
            Log::error('Profil enrichi - Contact api error');
            return false;
        }
    }

    public function callKasprApi($name,$profile_id)
    {
        // URL de l'API
        $url = env('KASPR_API_URL');

        // Récupérer le bearer token depuis le fichier .env
        $token = env('KASPR_API_TOKEN');

        // Le body de la requête
        $body = [
            "id" => $profile_id,
            "name" => $name,
            "isPhoneRequired"=>true,
            "dataToGet" => [
                "phone",
                "workEmail",
                // "directEmail"
            ]
        ];

        // Effectuer la requête POST avec le bearer token
        $response = Http::withToken($token)
            ->withHeaders(['accept-version' => 'v2.0'])
            ->post($url, $body);

        
        // log response
        $body = $response->body();

        // Vérifier si la requête a réussi
        if ($response->successful()) {
            // log response message
            Log::info('Profil enrichi - kaspr api response - '.json_encode($body));
            return $body;
        } else {
            // log response message
            Log::error('Profil enrichi - kaspr api error - '.json_encode($body));
            return false;
        }
    }


    // enrichProfil id
    public function enrichProfil_old($id)
    {
        $profil = Profil::find($id);
        if (!$profil) {
            return response()->json(array('message' => __("Profil introuvable")), 404);
        }

        $first_name = $profil->first_name;
        $last_name = $profil->last_name;
        $company = $profil->entreprise->name ?? '';
        $domain = $profil->entreprise->domain ?? '';
        // delete www. from domain
        $domain = str_replace('www.', '', $domain);

        if ($domain == "" || $domain == null || $domain == " ") {
            return response()->json(array('message' => __("Entreprise sans domaine")), 405);
        }

        // Diviser le last_name en prénoms potentiels
        $last_name_parts = explode(' ', $last_name);

        // Générer les combinaisons spécifiques
        $combinations = [];
        $combinations[] = [$first_name, $last_name_parts[0]];
        if (isset($last_name_parts[1])) {
            $combinations[] = [$first_name, $last_name_parts[1]];
        }
        if (isset($last_name_parts[2])) {
            $combinations[] = [$first_name, $last_name_parts[2]];
        }
        if (isset($last_name_parts[0], $last_name_parts[1])) {
            $combinations[] = [$last_name_parts[0], $last_name_parts[1]];
        }
        if (isset($last_name_parts[0], $last_name_parts[2])) {
            $combinations[] = [$last_name_parts[0], $last_name_parts[2]];
        }
        if (isset($last_name_parts[1], $last_name_parts[2])) {
            $combinations[] = [$last_name_parts[1], $last_name_parts[2]];
        }

        // Essayer chaque combinaison
        foreach ($combinations as $combination) {
            list($fn, $ln) = $combination;
            $email = $fn . '.' . $ln . '@' . $domain; 
            // replace é,è,ê,ë,à,â,ä,ç,ù,û,ü,ô,ö,î,ï by e,a,c,u,o,i
            $email = str_replace(['é', 'è', 'ê', 'ë'], 'e', $email);
            $email = str_replace(['à', 'â', 'ä'], 'a', $email);
            $email = str_replace('ç', 'c', $email);
            $email = str_replace(['ù', 'û', 'ü'], 'u', $email);
            $email = str_replace(['ô', 'ö'], 'o', $email);
            $email = str_replace(['î', 'ï'], 'i', $email);

            $email = strtolower($email);
            // $data_scraped = $this->verify($email);
            $is_valid = $this->verifyEmail($email);
            // $data_scraped = $this->hunt($fn, $ln, $domain);
            if ($is_valid) {
                $profil->email = $email;
                $profil->enrich_status = 'enriched';
                $profil->save();
                return response()->json(array('message' => __("Profil enrichi")), 200);
            }
        }

        // Si aucune combinaison n'a fonctionné
        $profil->enrich_status = 'cant_enriched';
        $profil->save();
        return response()->json(array('message' => __("Profil non enrichi")), 422);
    }

    // create a new tag 
    public function createTag(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:10',
        ],  $this->customMessages);

        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        // $color = $this->get_random_color();

        $tag = Tag::findOrCreate($request['name'], 'profil');

        return response()->json(array('message' => __("Tag ajoutée"), 'data' => new ProfilTagResource($tag)), 201);
    }
    // get all tags
    public function getTags()
    {
        $tags = Tag::getWithType('profil');
        // return $tags;
        return ProfilTagResource::collection($tags);
    }

    // update a tag 
    public function updateTag(Request $request, $id)
    {
        $tag = ProfilTag::find($id);

        if(!$tag){
            return response()->json(array('message' => __("Tag introuvable")), 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:10|unique:profil_tags,name',
        ],  $this->customMessages);

        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        $tag->update([
            'name' => $request['name'],
        ]);

        return response()->json(array('message' => __("Tag modifiée"), 'data' => new ProfilTagResource($tag)), 200);
    }

    public function get_random_color(){
        $colors = [
            'red' ,
            'green' ,
            'blue' ,
            'yellow' ,
            'orange' ,
            'purple' ,
            'pink' ,
            'black' ,
            'gray' ,
        ];


        $random = rand(0, count($colors) - 1);
        $color = $colors[$random];
        return $color;
    }

    // delete a tag 
    public function deleteTag($id)
    {
        $tag = ProfilTag::find($id);
        if(!$tag){
            return response()->json(array('message' => __("Tag introuvable")), 404);
        }

        $tag->delete();

        return response()->json(array('message' => __("Tag supprimée")), 200);
    } 
            
    public function verifyEmail($email) {
        // Get the domain of the email
        list($user, $domain) = explode('@', $email);

        // Get the MX records for the domain
        if (!getmxrr($domain, $mxHosts)) {
            return false; // No MX records found
        }

        // Get the first MX record
        $mxHost = $mxHosts[0];

        // Open an SMTP connection to the MX host
        $connect = @fsockopen($mxHost, 25);
        if (!$connect) {
            return false; // Unable to connect to the server
        }

        // Read the server response
        $response = fgets($connect, 1024);

        if (strpos($response, '220') === false) {
            return false; // Server did not respond with 220 code
        }

        // Say hello to the server
        fputs($connect, "HELO example.com\r\n");
        $response = fgets($connect, 1024);

        if (strpos($response, '250') === false) {
            return false; // Server did not respond with 250 code
        }

        // Tell the server the sender email
        fputs($connect, "MAIL FROM: <sender@example.com>\r\n");
        $response = fgets($connect, 1024);

        if (strpos($response, '250') === false) {
            return false; // Server did not respond with 250 code
        }

        // Ask the server if the recipient email exists
        fputs($connect, "RCPT TO: <$email>\r\n");
        $response = fgets($connect, 1024);

        // Close the connection
        fputs($connect, "QUIT\r\n");
        fclose($connect);

        // Check if the server response indicates the email exists
        if (strpos($response, '250') !== false || strpos($response, '450') !== false) {
            return true;
        }

        return false;
    }

    // getProfilsByTags
    public function getProfilsByTags($tag)
    {
       $profils =  Profil::withAllTags([$tag], 'profil')->get();

        // $profils = Profil::where('tags', 'like', '%'.$tag.'%')->get();
        return ProfilResource::collection($profils);
    }

    // detachTag
    public function detachTag($id, $tag)
    {
        $profil = Profil::find($id);
        if(!$profil){
            return response()->json(array('message' => __("Profil introuvable")), 404);
        }

        $profil->detachTag($tag,'profil');

        return response()->json(array('message' => __("Tag détachée")), 200);
    }
 

}