<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;
class Profil extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasTags;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'first_name',
        'last_name',
        'email',
        'email_two',
        'profile_id',
        'entreprise_id',
        'title',
        'linkedin',
        'telephone',
        'telephone_two',
        'email',
        'website',
        'adress',
        'city',
        'country',
        'postal_code',
        'about',
        'enrich_status',
        'tags',
        'kaspr_data',
        'cout_data',
        'is_enrich_cout',
    ];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    // append photo
    protected $appends = ['photo','can_enrich','tags_labels'];

    // 
    public function getTagsLabelsAttribute()
    {
        // $tags = $this->tags;
        // if(!$tags){
        //     return [];
        // }
        // $tags = json_decode($tags);
        // return $tags;

        $tags = $this->tags()->get();
        // return $tags;
        $return = [];
        foreach ($tags as $tag) {
            $return[] = $tag->name;
        }
        return $return;
    }

    public function getPhotoAttribute()
    {
        $photo =  $this->getFirstMediaUrl('photo') ? $this->getFirstMediaUrl('photo') : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRShs-5rOAO2ObBv7UzfsR4Qr_Nc9lfWn4ggedMi7H6GA&s';
        if($photo){
            $photo = str_replace('localhost', 'localhost:8000', $photo);
        }
        return $photo;
    }

    public function getCanEnrichAttribute()
    {
        $domain = $this->entreprise->domain ?? '';

        $profile_id = $this->profile_id ?? '';

        if($profile_id == "" || $profile_id == null || $profile_id == " "){
            return false;
        }

        // if($domain == "" || $domain == null || $domain == " "){
        //     return false;
        // }
        //    can not enrich if enrich_status is different to not_tried or profil email is not null or not empty
        return $this->enrich_status == 'not_tried' && ($this->email == null || $this->email == '') ? true : false;
    }

     

}
