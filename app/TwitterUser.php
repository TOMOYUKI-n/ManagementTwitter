<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwitterUser extends Model
{
    //
    protected $table = 'twitter_users';

    protected $fillable = [
        'user_id', 'token', 'token_secret', 'screen'
    ];

    protected $visible = [
        'id'
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer'
    ];

    /**
     * usersテーブル
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * follow_targetsテーブル
     */
    public function followTarget()
    {
        return $this->hasMany('App\FollowTarget', 'twitter_user_id');
    }

    /**
     * system_managersテーブル
     */
    public function systemManagers()
    {
        return $this->hasMany('App\SystemManager');

    }
}
