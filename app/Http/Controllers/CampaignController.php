<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignResource; 
use App\Http\Resources\CampaignResourceLite;
use App\Models\Campaign;
use App\Models\Profil;
use App\Models\UserCampaign;
use App\Models\Entreprise;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// log facade
use Illuminate\Support\Facades\Log;
 
class CampaignController extends Controller
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
    //   crud for campaigns
    public function index(Request $request)
    {
        // get campaigns order by id desc paginated 10
        $campaigns = Campaign::orderBy('id', 'desc')->paginate(10);
        return CampaignResource::collection($campaigns);
    }

    // store
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:campaigns,title',
            'subject' => 'required|string|max:255',
            'tags' => 'nullable|string',
            'content' => 'required|string',
            'files' => 'nullable|string',
            // 'status' => 'required|string|max:255',
        ], $this->customMessages);
        
        $errors = $validator->errors();
        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        $campaign = Campaign::create([
            'title' => $request['title'] ?? '',
            'subject' => $request['subject'] ?? '',
            'tags' => $request['tags'] ?? '',
            'content' => $request['content'] ?? '',
            'files' => $request['files'] ?? '', 
            'status'=>'draft',
            'user_id' => Auth::user()->id,
        ]);

        if ($request->hasFile('attachments')) {
            $campaign->clearMediaCollection('attachments');
            foreach ($request->file('attachments') as $file) {
                $campaign->addMedia($file)
                        ->toMediaCollection('attachments');
            }
        }
 
        
        $campaign->save();
        return response()->json(array('message' => __("Campaign ajouté"), 'data' => new CampaignResource($campaign)), 200);
    }

    // update
    public function update(Request $request, $id)
    {
        $campaign = Campaign::find($id);

        if(!$campaign){
            return response()->json(array('message' => __("Campaign introuvable")), 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'tags' => 'nullable|string',
            'content' => 'required|string', 
            'files' => 'nullable|string',
        ], $this->customMessages);
        
        $errors = $validator->errors();


        if ($errors->count()) {
            return response()->json(array('message' => $errors->first(), 'data' => $errors), 422);
        }

        
        $campaign->fill($request->all());

        if ($request->hasFile('attachments')) {
            $campaign->clearMediaCollection('attachments');
            foreach ($request->file('attachments') as $file) {
                $campaign->addMedia($file)->toMediaCollection('attachments');
            }
        }

        $campaign->sent_at = null;
        $campaign->save();
        return response()->json(array('message' => __("Campaign modifié"), 'data' => new CampaignResource($campaign)), 200);
    }

    // delete
    public function destroy($id)
    {
        $campaign = Campaign::find($id);
        if(!$campaign){
            return response()->json(array('message' => __("Campaign introuvable")), 404);
        }
        $campaign->delete();
        return response()->json(array('message' => __("Campaign supprimé")), 200);
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $entreprises = Campaign::where('title', 'like', '%'.$search.'%')
            ->orWhere('subject', 'like', '%'.$search.'%')
            ->orWhere('tags', 'like', '%'.$search.'%')
            ->paginate(10);
        return CampaignResource::collection($entreprises);
    }

    // launch
    public function launch($id)
    {
        $campaign = Campaign::find($id);
        if(!$campaign){
            return response()->json(array('message' => __("Campaign introuvable")), 404);
        } 
        
        if($campaign->status == 'launched'){
            // return response()->json(array('message' => __("Campaign déjà lancée")), 422);
        }


        $tags = $campaign->tags;

        $subject = $campaign->subject;

        $content = $campaign->content;

        $attachments = $campaign->getMedia('attachments');

        $email_attachments = [];
        foreach ($attachments as $attachment) {
            $email_attachments[] = [
                'path' => $attachment->original_url,
                'url' => $attachment->original_url,
                'name' => $attachment->file_name, 
                'mime' => "application/".$attachment->extension  
            ];
        }

        $profils = Profil::where('tags', 'like', '%'.$tags.'%')->get();
        $o365_mail = new Office365MailController();
        $total_sent = 0;
        $total_profils = count($profils);

        foreach($profils as $profil){
            if($profil->email == null || $profil->email == ''){
                continue;
            }
            $to = $profil->email;
            $the_content = $content;
            // replace [NOM] by profil first_name
            $the_content = str_replace('[NOM]', $profil->first_name, $the_content);
            // replace [PRENOM] by profil last_name
            $the_content = str_replace('[PRENOM]', $profil->last_name, $the_content);

            $send = $o365_mail->sendMail($to, $subject, $the_content);

            if($send !==true){
                // log error
                Log::error('Erreur lors de l\'envoi de l\'email à '.$profil->first_name.' : '.$send);
            }

            $total_sent++;

        }

        $campaign->sent_at = now();
        $campaign->status = 'launched';
        $campaign->save();

        return response()->json(array('message' => __("Campagne lancée avec succès ".$total_sent."/".$total_profils." emails envoyés"), 'total_sent' => $total_sent,'total_profils'=>$total_profils), 200);



    }


}