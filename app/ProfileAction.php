<?php

namespace App;

use App\Traits\FormatDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ProfileAction extends Model
{
    use FormatDates;

    protected $dates = [
        'created_at',
        'updated_at',
    ];




    /*public function getCreatedAtAttribute($value)
    {
        //$date = Carbon::parse($value)->format('d/m/Y, h:m A'); // now date is a carbon instance
        // $elapsed = $date->diffForHumans(Carbon::now());
        $date = Carbon::parse($value)->diffForHumans(Carbon::now());
        return $date;
    }*/

}
