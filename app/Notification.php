<?php

namespace App;

use App\Traits\FormatDates;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    use FormatDates;

    protected $guarded = [];
    public function users(){
        return $this->belongsTo('App\User', 'action_by', 'id');
    }

    public function getCreatedAtAttribute($value){

        if(\Illuminate\Support\Facades\Request::route()->getName() === 'notifications'){
            $date = Carbon::parse($value)->timezone(Auth::user()->time_zone)->diffForHumans();
            return $date;
        }
        return $value;
    }
}
