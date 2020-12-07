<?php

namespace App;

use App\Traits\FormatDates;
use Illuminate\Database\Eloquent\Model;

class FeedBack extends Model
{
    use FormatDates;

    protected $table = 'feedback';

    public function user(){
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}


