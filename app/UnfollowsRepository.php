<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnfollowsRepository extends Model
{
    
    protected $table = 'unfollows_repository';
    protected $casts = [
        'twitter_user_id' => 'integer',
        'id' => 'integer'
    ];
}
