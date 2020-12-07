<?php

namespace App;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use Notifiable;
    protected $fillable = [
        'first_name', 'last_name', 'phone', 'email', 'address',  'city',  'state',  'zip_code',  'password',  'profile_image',
        'login_type', 'social_token', 'time_zone', 'fcm_token', 'role_id', 'package_id', 'token', 'lat', 'long'
    ];
    protected $hidden = [
        'password', 'remember_token', 'token'
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts(){
        return $this->hasMany('App\Post', 'created_by');
    }

    public function InterestedService(){
        return $this->belongsTo('App\InterestedService', 'service_id', 'id');
    }

    public function Interested_animal(){
        return $this->hasMany('App\InterestedAnimal', 'user_id', 'id');
    }

    public function timeOfAction(){
        return $this->hasOne('App\ProfileAction', 'action_to');
    }

    public function role(){
        return $this->hasOne('App\Role', 'id', 'role_id');
    }
    public function notifications(){
        return $this->hasMany('App\Notification', 'action_by', 'id');
    }


//    user profile
    public function user_personal_info(){
        return $this->hasOne('App\AboutMe', 'user_id');
    }
    public function pets(){
        return $this->hasMany('App\AboutPet', 'user_id');
    }
    public function hobbies(){
        return $this->hasMany('App\Hobbie', 'user_id');
    }
    public function interests(){
        return $this->hasMany('App\Interest', 'user_id');
    }
    public function LikedAllergicAnimal(){
        return $this->hasMany('App\LikedAllergicAnimal', 'user_id');
    }
    public function alerts(){
        return $this->hasMany('App\Alert', 'created_by');
    }




//    public function getCreatedAtAttribute($value){
//        $date = Carbon::parse($value)->timezone(Auth::user()->time_zone)->format('d/m/Y, h:m A'); // now date is a carbon instance
//        return $date;
//    }
//    public function setCreatedAtAttribute($value){
//        $c = Carbon::now();
//        $c->timezone = Auth::user()->time_zone;
//        $c->timezone('UTC');
//        $this->attributes['created_at'] = $c;
//    }


//    public function imageAs(){
//        return asset('/project-assets/images/'.$this->profile_image);
//    }







}
