<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FollowerTarget extends Model
{
    protected $casts = [
        'status' => 'integer'
     ];
 
     /**
      * リレーションシップ - twitter_usersテーブル
      */
     public function twitterUser()
     {
         return $this->belongsTo('App\TwitterUser', 'twitter_user_id');
     }
}
