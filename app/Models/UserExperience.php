<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExperience extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entreprise',
        'title',
        'project',
        'start_date',
        'end_date',
        'mission',
        'envs',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
