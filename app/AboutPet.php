<?php

namespace App;

use App\Traits\FormatDates;
use Illuminate\Database\Eloquent\Model;

class AboutPet extends Model
{
    use FormatDates;
    protected $guarded = [];


    public function getImageAttribute($value){
        if(\Illuminate\Support\Facades\Request::route()->getName() === 'pet-listing'){
            return asset('/project-assets/images/'.$value);
        }
        return $value;
    }



}
