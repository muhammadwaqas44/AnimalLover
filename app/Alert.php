<?php

namespace App;

use App\Traits\FormatDates;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use FormatDates;

    public function alerts_images(){
        return $this->hasMany('App\AlertGallery', 'alert_id');
    }

    public function user(){
        return $this->hasOne('App\User', 'id', 'created_by');
    }
}
