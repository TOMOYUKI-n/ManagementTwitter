<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnfollowDetect extends Model
{
    protected $table = 'unfollow_detect';

    protected $fillable = [
        'twitter_user_id', 'twitter_id',
    ];
  
    protected $casts = [
        'twitter_user_id' => 'integer',
        'id' => 'integer'
    ];
}
