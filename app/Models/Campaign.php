<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Campaign extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'title',
        'user_id',
        'subject',
        'tags',
        'content',
        'files',
        'status',  
        'sent_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // attachments

     

}
