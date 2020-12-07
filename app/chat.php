<?php

namespace App;

use App\Traits\FormatDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class chat extends Model
{
    use FormatDates;

    public function getCreatedAtAttribute($value){
        $date = Carbon::parse($value)->timezone(Auth::user()->time_zone)->diffForHumans(); // now date is a carbon instance
        return $date;
    }

    public function sender(){
        return $this->hasOne('App\User', 'id', 'sender_id');
    }
    public function receiver(){
        return $this->hasOne('App\User', 'id', 'receiver_id');
    }
}
