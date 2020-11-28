<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FollowsRepository extends Model
{
    protected $table = 'follows_repository';
    protected $casts = [
        'twitter_user_id' => 'integer',
    ];
}
