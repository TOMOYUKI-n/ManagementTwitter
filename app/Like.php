<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $table = 'likes';
    protected $hidden = [
        'created_at', 'updated_at', 'user_id', 'twitter_user_id','keyword_id'
    ];

    /**
     * usersテーブル
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * twitter_usersテーブル
     */
    public function twitterUser()
    {
        return $this->belongsTo('App\TwitterUser', 'twitter_user_id');
    }

    /**
     * filter_wordsテーブル
     */
    public function keyword()
    {
        return $this->belongsTo('App\Keyword', 'keyword_id');
    }
}
