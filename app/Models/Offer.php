<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Offer extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'title',
        'description',
        'experience_years',
        'category',
        'country',
        'city',
        'status',
        'remote',
        'type',
        'industry',
    ];

    // append photo
    protected $appends = ['photo','photoImg'];

    public function getphotoAttribute()
    {
        $photo =  $this->getFirstMediaUrl('photo') ? $this->getFirstMediaUrl('photo') : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRShs-5rOAO2ObBv7UzfsR4Qr_Nc9lfWn4ggedMi7H6GA&s';
        if($photo){
            $photo = str_replace('localhost', 'localhost:8000', $photo);
        }
        return $photo;
    }

    public function getphotoImgAttribute()
    {
        $photo =  $this->getFirstMediaUrl('photo') ? $this->getFirstMediaUrl('photo') : null;
        if($photo){
            $photo = str_replace('localhost', 'localhost:8000', $photo);
        }
        return $photo;
    }
 
     

}
