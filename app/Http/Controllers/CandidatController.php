<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\CandidatResource; 
use App\Http\Resources\EtapeResource; 
use App\Models\Candidat;
use App\Models\Offer;
use App\Models\Entreprise;
use App\Models\Etape;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

 
class CandidatController extends Controller
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

//    crud for candidats

    public function index(Request $request)
    {
        // get candidats order by id desc paginated 10
        $candidats = Candidat::orderBy('id', 'desc')->paginate(10);
        return CandidatResource::collection($candidats);
    }

    // store
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // first_name,last_name,email,email_s,telephone,telephone_s,city,country,linkedin,adress,last_situation,entreprise_id,current_client_id,contrat_type,contrat_start,contrat_end,tjm,sal_net,sal_brut,status_ano,status,comment
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'email_s' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'telephone_s' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'adress' => 'nullable|string|max:255',
            'last_situation' => 'nullable|string|max:255',
            'entreprise_id' => 'nullable|string|max:255',
            'current_client_id' => 'nullable|string|max:255',
            'contrat_type' => 'nullable|string|max:255',
            'contrat_start' => 'nullable|string|max:255',
            'contrat_end' => 'nullable|string|max:255',
            'tjm' => 'nullable|string|max:255',
            'sal_net' => 'nullable|string|max:255',
            'sal_brut' => 'nullable|string|max:255',
            'status_ano' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:255',
            'etape_id' => 'nullable|exists:etapes,id',
            'statut_matrimonial' => 'nullable|string|max:255',
            'annee_experience' => 'nullable|string|max:255',
            'expertise_technique' => 'nullable|string|max:255',
            'clients' => 'nullable|string|max:255',
            'langues' => 'nullable|string|max:255',
            'etl' => 'nullable|string|max:255',
            'pretentions_salariales' => 'nullable|string|max:255',
            'certifications' => 'nullable|string|max:255',
            'gestion_projet' => 'nullable|string|max:255',
            'date_naissance'=>'nullable',
            'preference_localisation'=>'nullable',
            'poste_actuel'=>'nullable',
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        $candidat = Candidat::create([
            'first_name' => $request['first_name'] ?? "",
            'last_name' => $request['last_name'] ?? "",
            'email' => $request['email'] ?? "",
            'email_s' => $request['email_s'] ?? "",
            'telephone' => $request['telephone'] ?? "",
            'telephone_s' => $request['telephone_s'] ?? "",
            'city' => $request['city'] ?? "",
            'country' => $request['country'] ?? "",
            'linkedin' => $request['linkedin'] ?? "",
            'adress' => $request['adress'] ?? "",
            'last_situation' => $request['last_situation'] ?? "",
            'entreprise_id' => $request['entreprise_id'] ?? "",
            'current_client_id' => $request['current_client_id'] ?? "",
            'contrat_type' => $request['contrat_type'] ?? "",
            'contrat_start' => $request['contrat_start'] ?? "",
            'contrat_end' => $request['contrat_end'] ?? "",
            'tjm' => $request['tjm'] ?? "",
            'sal_net' => $request['sal_net'] ?? "",
            'sal_brut' => $request['sal_brut'] ?? "",
            'status_ano' => $request['status_ano'] ?? "",
            'status' => $request['status'] ?? "",
            'comment' => $request['comment'] ?? "",
            'etape_id' => $request['etape_id'] ?? 1,
            'statut_matrimonial' => $request['statut_matrimonial'] ?? "",
            'annee_experience' => $request['annee_experience'] ?? "",
            'expertise_technique' => $request['expertise_technique'] ?? "",
            'clients' => $request['clients'] ?? "",
            'langues' => $request['langues'] ?? "",
            'etl' => $request['etl'] ?? "",
            'pretentions_salariales' => $request['pretentions_salariales'] ?? "",
            'certifications' => $request['certifications'] ?? "",
            'gestion_projet' => $request['gestion_projet'] ?? "",
            'date_naissance'=>$request['date_naissance'] ?? "",
            'preference_localisation'=>$request['preference_localisation'] ?? "",
            'poste_actuel'=>$request['poste_actuel'] ?? "",
        ]);

        // if photo add photo to media 
        if($request->hasFile('photo')){
            $candidat->addMedia($request->file('photo'))->toMediaCollection('photo');
        }

        // if resume add resume to media
        if($request->hasFile('resume')){
            $candidat->addMedia($request->file('resume'))->toMediaCollection('resume');
        }

        $candidat->save();
        return response()->json(array('message' => __("Candidat ajouté"), 'data' => new CandidatResource($candidat)), 200);
    }

    // update
    public function update(Request $request, $id)
    {
        $candidat = Candidat::find($id);

        if(!$candidat){
            return response()->json(array('message' => __("Candidat introuvable")), 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'email_s' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'telephone_s' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'adress' => 'nullable|string|max:255',
            'last_situation' => 'nullable|string|max:255',
            'entreprise_id' => 'nullable|string|max:255',
            'current_client_id' => 'nullable|string|max:255',
            'contrat_type' => 'nullable|string|max:255',
            'contrat_start' => 'nullable|string|max:255',
            'contrat_end' => 'nullable|string|max:255',
            'tjm' => 'nullable|string|max:255',
            'sal_net' => 'nullable|string|max:255',
            'sal_brut' => 'nullable|string|max:255',
            'status_ano' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:255',
            'etape_id' => 'nullable|exists:etapes,id',
            'statut_matrimonial' => 'nullable|string|max:255',
            'annee_experience' => 'nullable|string|max:255',
            'expertise_technique' => 'nullable|string|max:255',
            'clients' => 'nullable|string|max:255',
            'langues' => 'nullable|string|max:255',
            'etl' => 'nullable|string|max:255',
            'pretentions_salariales' => 'nullable|string|max:255',
            'certifications' => 'nullable|string|max:255',
            'gestion_projet' => 'nullable|string|max:255',
            'date_naissance'=>'nullable',
            'preference_localisation'=>'nullable',
            'poste_actuel'=>'nullable',
        ], $this->customMessages);
        
        $errors = $validator->errors();

        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        //  fill the request data
        $candidat->fill($request->all());

        // if photo add photo to media
        if($request->hasFile('photo')){
            $candidat->addMedia($request->file('photo'))->toMediaCollection('photo');
        }

        // if resume add resume to media
        if($request->hasFile('resume')){
            // remove old resume
            $candidat->media()->where('collection_name', 'resume')->delete();
            $candidat->addMedia($request->file('resume'))->toMediaCollection('resume');
        }


        $candidat->save();

        return response()->json(array('message' => __("Candidat modifié"), 'data' => new CandidatResource($candidat)), 200);
    }

    // delete
    public function destroy($id)
    {
        $candidat = Candidat::find($id);
        if(!$candidat){
            return response()->json(array('message' => __("Candidat introuvable")), 404);
        }

        $candidat->delete();

        return response()->json(array('message' => __("Candidat supprimé")), 200);
    }
 

    public function search(Request $request)
    {
        $search = $request->search;

        $etapes  = $request->etapes;

        $type_contrat = $request->type_contrat;

        $min_exp = $request->min_exp;

        $max_exp = $request->max_exp;

        if(!$etapes){
           $etapes  = Etape::pluck('id');
        }

        $candidats = Candidat::Where(function($query) use ($etapes){
            $query->whereIn('etape_id', $etapes);
        });

        if($search!=''){
            $candidats = $candidats->where(function($query) use ($search) {
                $query->orWhere('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%')
                    ->orWhere('telephone', 'like', '%'.$search.'%')
                    ->orWhere('telephone_s', 'like', '%'.$search.'%')
                    ->orWhere('adress', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('email_s', 'like', '%'.$search.'%')
                    ->orWhere('city', 'like', '%'.$search.'%')
                    ->orWhere('country', 'like', '%'.$search.'%')
                    ->orWhere('last_situation', 'like', '%'.$search.'%');
            });
        }

        if($type_contrat!=''){
            $candidats = $candidats->where('contrat_type', $type_contrat);
        }

        if($min_exp!=''){
            $candidats = $candidats->where('annee_experience', '>=', $min_exp);
        }

        if($max_exp!=''){
            $candidats = $candidats->where('annee_experience', '<=', $max_exp);
        }

        // if($search==''){
        //     $candidats = Candidat::Where(function($query) use ($etapes){
        //         $query->whereIn('etape_id', $etapes);
        //     })->paginate(10);
        // }else{
        //     $candidats = Candidat::whereIn('etape_id', $etapes)
        //     ->where(function($query) use ($search) {
        //         $query->orWhere('first_name', 'like', '%'.$search.'%')
        //             ->orWhere('last_name', 'like', '%'.$search.'%')
        //             ->orWhere('telephone', 'like', '%'.$search.'%')
        //             ->orWhere('telephone_s', 'like', '%'.$search.'%')
        //             ->orWhere('adress', 'like', '%'.$search.'%')
        //             ->orWhere('email', 'like', '%'.$search.'%')
        //             ->orWhere('email_s', 'like', '%'.$search.'%')
        //             ->orWhere('city', 'like', '%'.$search.'%')
        //             ->orWhere('country', 'like', '%'.$search.'%')
        //             ->orWhere('last_situation', 'like', '%'.$search.'%');
        //     })
        //     ->paginate(10);
        // }

        $candidats = $candidats->paginate(10);

        // return $etapes;
        return CandidatResource::collection($candidats);
    }

    // getAllEtapes
    public function getAllEtapes()
    {
        $etapes = Etape::all();
        return EtapeResource::collection($etapes);
    }

    public function get_candidats_by_offer($offer_id){
        $offer = Offer::find($offer_id);

        if(!$offer){
            return response()->json(array('message' => __("Offre introuvable")), 404);
        }

        $exp_years = $offer->experience_years;

        $category = $offer->category;

        $industry = $offer->industry;

        $candidats = Candidat::where('annee_experience','>=',$exp_years)
            ->orWhere('certifications', 'like', '%'.$industry.'%')
            ->orWhere('expertise_technique', 'like', '%'.$industry.'%')
            ->orWhere('etl', 'like', '%'.$industry.'%')
            ->orWhere('gestion_projet', 'like', '%'.$industry.'%')
            ->orWhere('certifications', 'like', '%'.$category.'%')
            ->orWhere('expertise_technique', 'like', '%'.$category.'%')
            ->orWhere('etl', 'like', '%'.$category.'%')
            ->orWhere('gestion_projet', 'like', '%'.$category.'%')
            ->paginate(10);

        return CandidatResource::collection($candidats);

        // expertise_technique, certifications, etl, gestion_projet


    }

    public function show($id){
        $candidat = Candidat::find($id);
        if(!$candidat){
            return response()->json(array('message' => __("Candidat introuvable")), 404);
        }
        return new CandidatResource($candidat);
    }

}