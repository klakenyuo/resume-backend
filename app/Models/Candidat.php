<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Candidat extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'first_name',
        'last_name',
        'email',
        'email_s',
        'telephone',
        'telephone_s',
        'city',
        'country',
        'linkedin',
        'adress',
        'last_situation',
        'entreprise_id',
        'current_client_id',
        'contrat_type',
        'contrat_start',
        'contrat_end',
        'tjm',
        'sal_net',
        'sal_brut',
        'status_ano',
        'status',
        'comment',
        'etape_id',
        'statut_matrimonial',
        'annee_experience',
        'expertise_technique',
        'clients',
        'langues',
        'etl',
        'pretentions_salariales',
        'certifications',
        'gestion_projet',
        'date_naissance',
        'preference_localisation',
        'poste_actuel',
    ];

    // append photo
    protected $appends = ['photo','photoImg','resume','resumeImg'];
    public function getPhotoAttribute()
    {
        $photo =  $this->getFirstMediaUrl('photo') ? $this->getFirstMediaUrl('photo') : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRShs-5rOAO2ObBv7UzfsR4Qr_Nc9lfWn4ggedMi7H6GA&s';
        if($photo){
            $photo = str_replace('localhost', 'localhost:8000', $photo);
        }
        return $photo;
    }

    public function getPhotoImgAttribute()
    {
        $photo =  $this->getFirstMediaUrl('photo') ? $this->getFirstMediaUrl('photo') : null;
        if($photo){
            $photo = str_replace('localhost', 'localhost:8000', $photo);
        }
        return $photo;
    }

    public function getResumeImgAttribute()
    {
        $resume =  $this->getFirstMediaUrl('resume') ? $this->getFirstMediaUrl('resume') : null;
        if($resume){
            $resume = str_replace('localhost', 'localhost:8000', $resume);
        }
        return $resume;
    }

    public function getResumeAttribute()
    {
        $resume =  $this->getFirstMediaUrl('resume') ? $this->getFirstMediaUrl('resume') : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRShs-5rOAO2ObBv7UzfsR4Qr_Nc9lfWn4ggedMi7H6GA&s';
        if($resume){
            $resume = str_replace('localhost', 'localhost:8000', $resume);
        }
        return $resume;
    }

    public function etape()
    {
        return $this->belongsTo(Etape::class);
    }
 
     

}
