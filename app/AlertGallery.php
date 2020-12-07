<?php

namespace App;

use App\Traits\FormatDates;
use Illuminate\Database\Eloquent\Model;

class AlertGallery extends Model
{
    use FormatDates;

    protected $table = 'alert_gallery';

    public function getNameAttribute($value){
        return asset('/project-assets/images/'.$value);
    }
}
