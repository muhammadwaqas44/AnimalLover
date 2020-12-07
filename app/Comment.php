<?php

namespace App;

use App\Traits\FormatDates;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use FormatDates;

    public function user(){
        return $this->belongsTo('App\User');
    }
}
