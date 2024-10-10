<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{   
    public function toArray($request)
    {
        return [  
            'id' => $this->id,
            'title' => $this->title,
            'subject' => $this->subject,
            'tags' => $this->tags,
            'content' => $this->content,
            'files' => $this->files,
            // 'status' => $this->status,    
            'status' => 'draft',    
            'user'=> New UserResource($this->user),  
            // created_at in french
            'created_at' => $this->created_at->format('d F Y'),
            // sent_at is timestamp .. convert to french
            'sent_at' => $this->sent_at,
            'status_label'=> $this->status == 'draft' ? 'En attente' : 'Lancée',
            'status_color'=> $this->status == 'draft' ? 'orange' : 'green', 
            'attachments' => $this->getAttachments(),
        ];  
    }

    // get attachments
    public function getAttachments()
    {

        $urls = [];
        $attachments = $this->getMedia('attachments');


        return $attachments;

        // Exemple d'affichage des URLs des fichiers attachés
        foreach ($attachments as $attachment) {
            $urls[] = $attachment->getUrl();
        }
        
        return $urls;
    }


    
}
