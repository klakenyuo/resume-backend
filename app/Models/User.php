<?php

namespace App\Models;
use Carbon\Carbon;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\UserLesson;
use App\Models\Course;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'telephone',
        'address',
        'birth_date',
        'exp_years',
        'role',
        'linkedin',
        'verification_code',
        'isActive',
        'isAdmin',
        'password',
        'email_verified_at',
        'birth_place',
        'nationality',
        'adress',
        'iban',
        'tel_one',
        'tel_two',
        'comments',
        'society',
        'entry_date',
        'tjm',
        'is_login_office',
        'auth_code',
        'refresh_token',
        'color',
    ];

    protected $appends = ['photo','photoImg'];
 
    public function getPhotoAttribute()
    {
        $photo =  $this->getFirstMediaUrl('photo') ? $this->getFirstMediaUrl('photo') : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRShs-5rOAO2ObBv7UzfsR4Qr_Nc9lfWn4ggedMi7H6GA&s';
        if($photo){
            // replace localhost with localhost:8000 in $photo
            $photo = str_replace('localhost', 'localhost:8000', $photo);
        }
        return $photo;
    }

    public function getPhotoImgAttribute()
    {
        $photo =  $this->getFirstMediaUrl('photo') ? $this->getFirstMediaUrl('photo') : null;
        if($photo){
            // replace localhost with localhost:8000 in $photo
            $photo = str_replace('localhost', 'localhost:8000', $photo);
        }
        return $photo;
    }


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'isActive' => 'boolean',
        'isAdmin' => 'boolean',
    ];

    // formations
    public function formations()
    {
        return $this->hasMany(UserFormation::class);
    }

    // certifications
    public function certifications()
    {
        return $this->hasMany(UserMeta::class)->where('type', 'certification');  
    }

    // expertises
    public function expertises()
    {
        return $this->hasMany(UserMeta::class)->where('type', 'expertise');  
    }

    // interests
    public function interests()
    {
        return $this->hasMany(UserMeta::class)->where('type', 'interest');  
    }

    public function skills()
    {
        return $this->hasMany(UserMeta::class)->whereIn('type', ['tskill', 'mskill']);
    }

    public function mskills()
    {
        return $this->hasMany(UserMeta::class)->where('type', 'mskill');  
    }

    public function tskills()
    {
        return $this->hasMany(UserMeta::class)->where('type', 'tskill');  
    }

    // experiences
    public function experiences()
    {
        return $this->hasMany(UserExperience::class);
    }

    // projects
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'user_projects', 'user_id', 'project_id');
    }

    // permissions
    public function permissions()
    {
        return  $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id');
         
    }

    // default permission
    public function defaultPermission()
    {
        return Permission::where('name', 'timesheet')->get();
    }

    public function get_colors(){
        $colors = array(
            "#9A7B0C",
            "#8B0000",
            "#1D2951",
            "#0B3D0B",
            "#8B4500",
            "#4E342E",
            "#4B0082",
        );
        return $colors;
    }

    public function my_color(){
        $user_color = $this->color;
        if(!$user_color){
            $user_color = $this->get_colors()[mt_rand(0, count($this->get_colors()) - 1)];
            $this->color = $user_color;
            $this->save();
        }
        return $user_color;
    }
     
    public function my_initials(){

       $first_name = $this->first_name;
       $last_name = $this->last_name;
       $initials =  $first_name[0].$last_name[0];

        // return uppercase initials
       return strtoupper($initials);

    }
 
}
