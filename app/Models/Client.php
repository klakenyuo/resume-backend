<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Client extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        // clients(name,website,domain,country,city,adress,contact,telephone,email)
        'name',
        'website',
        'domain',
        'country',
        'city',
        'adress',
        'contact',
        'telephone',
        'email',
    ];

    // append logo
    protected $appends = ['logo','logoImg'];

    public function getLogoAttribute()
    {
        $logo =  $this->getFirstMediaUrl('logo') ? $this->getFirstMediaUrl('logo') : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRShs-5rOAO2ObBv7UzfsR4Qr_Nc9lfWn4ggedMi7H6GA&s';
        if($logo){
            $logo = str_replace('localhost', 'localhost:8000', $logo);
        }
        return $logo;
    }

    public function getLogoImgAttribute()
    {
        $logo =  $this->getFirstMediaUrl('logo') ? $this->getFirstMediaUrl('logo') : null;
        if($logo){
            $logo = str_replace('localhost', 'localhost:8000', $logo);
        }
        return $logo;
    }
 
     

}
