<?php

namespace App;

use App\Traits\FormatDates;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Conversation extends Model
{
    use FormatDates;
//
    public function getCreatedAtAttribute($value){
        $date =   Carbon::parse($value)->timezone(Auth::user()->time_zone)->format('d/m/Y, h:m A'); // now date is a carbon instance
        return $date;
    }

    public function last_message(){
        return $this->hasOne('App\chat', 'conversation_id', 'id')->orderBy('created_at', 'desc')->latest();
    }
}
