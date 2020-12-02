<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnfollowTarget extends Model
{
    protected $table = 'Unfollow_targets';
    protected $casts = [
        'twitter_user_id' => 'integer',
        'id' => 'integer'
    ];
}
