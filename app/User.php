<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $visible = [
        'id', 'name', 'email'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * twitter_usersテーブル
     */
    public function twitterUsers()
    {
        return $this->hasMany('App\TwitterUser', 'user_id');
    }

    /**
     * keywordsテーブル
     */
    public function keywords()
    {
        return $this->hasMany('App\Keyword', 'user_id');
    }

    /**
     * follow_targetsテーブル
     */
    public function followTargets()
    {
        return $this->hasMany('App\FollowTarget', 'user_id');
    }

    /**
     * system_managersテーブル
     */
    public function systemManagers()
    {
        return $this->hasMany('App\SystemManages', 'user_id');
    }
}
