<?php

namespace App;

use App\Traits\FormatDates;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
//    protected $guarded = [];

    use FormatDates;

    public function createdBy(){
        return $this->belongsTo('App\User', 'created_by');
    }

    public function comments(){
        return $this->hasMany('App\Comment');
    }
    public function WallpostComments(){
        return $this->hasMany('App\Comment');
    }

    public function gallery(){
        return $this->hasMany('App\GalleryImage');
    }
    public function images(){
        return $this->hasMany('App\GalleryImage');
    }

    public function likes(){
        return $this->hasMany('App\UserLikePost', 'post_id');
    }
    public function Wallpostlikes(){
        return $this->hasMany('App\UserLikePost', 'post_id');
    }
}
