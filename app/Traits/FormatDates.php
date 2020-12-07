<?php
/**
 * Created by PhpStorm.
 * User: Bilal Mahoon
 * Date: 28/11/2017
 * Time: 18:46
 */

namespace App\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

trait FormatDates
{

//    public function getCreatedAtAttribute($value){
//        $date = Carbon::parse($value)->timezone(Auth::user()->time_zone)->format('d/m/Y, h:m A'); // now date is a carbon instance
//        return $date;
//    }

    public function setCreatedAtAttribute($value){
        $c = Carbon::now();
        $c->timezone = Auth::user()->time_zone;
        $c->timezone('UTC');
        $this->attributes['created_at'] = $c;
    }
}
