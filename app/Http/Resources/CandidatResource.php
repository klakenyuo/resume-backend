<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CandidatResource extends JsonResource
{   
    public function toArray($request)
    {
        return [  
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'email_s' => $this->email_s,
            'telephone' => $this->telephone,
            'telephone_s' => $this->telephone_s,
            'city' => $this->city,
            'country' => $this->country,
            'linkedin' => $this->linkedin,
            'adress' => $this->adress,
            'last_situation' => $this->last_situation,
            'entreprise_id' => $this->entreprise_id,
            'current_client_id' => $this->current_client_id,
            'contrat_type' => $this->contrat_type,
            'contrat_start' => $this->contrat_start,
            'contrat_end' => $this->contrat_end,
            'tjm' => $this->tjm,
            'sal_net' => $this->sal_net,
            'sal_brut' => $this->sal_brut,
            'status_ano' => $this->status_ano,
            'status' => $this->status,
            'comment' => $this->comment,
            'photo' => $this->photo,
            'photoImg'=> $this->photoImg,
            'etape' => $this->etape,
            'etape_id' => $this->etape_id,  
            'resume' => $this->resume,
            'resumeImg' => $this->resumeImg,
            'statut_matrimonial' => $this->statut_matrimonial,
            'annee_experience' => $this->annee_experience,
            'expertise_technique' =>  $this->expertise_technique == "null" || $this->expertise_technique == "[]" ? null : $this->expertise_technique,
            'clients' =>  $this->clients == "null" || $this->clients == "[]" ? null : $this->clients,
            'langues' =>  $this->langues == "null" || $this->langues == "[]" ? null : $this->langues,
            'etl' => $this->etl == "null" || $this->etl == "[]" ? null : $this->etl,
            'pretentions_salariales' =>  $this->pretentions_salariales == "null" || $this->pretentions_salariales == "[]" ? null : $this->pretentions_salariales,
            'certifications' => $this->certifications == "null" || $this->certifications == "[]" ? null : $this->certifications,
            'gestion_projet' =>      $this->gestion_projet == "null" || $this->gestion_projet == "[]" ? null : $this->gestion_projet,
        ];  
    }
 


    
}
