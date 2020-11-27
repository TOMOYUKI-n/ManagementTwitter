<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Tweet extends Model
{
    /**
     * ステータスを定義
     */
    const STATUS = [
        1 => ['label' => '未送信'],
        2 => ['label' => 'ツイート済'],
    ];

    protected $casts = [
        'status' => 'integer'
    ];

    protected $appends = [
        'status_label', 'format_date', 'jp_format_date'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'date'
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
     * アクセサ
     * tatus_label
     */
    public function getStatusLabelAttribute()
    {
        $status = $this->attributes['status'];

        if (!isset(self::STATUS[$status])) {
            return '';
        }

        return self::STATUS[$status]['label'];
    }

    /**
     * アクセサ
     * format_date
     */
    public function getFormatDateAttribute()
    {
        $dates = $this->attributes['date'];
        $date = new \DateTime($dates);
        return $date->format('Y-m-d H:i');
    }

    /**
     * アクセサ
     * jp_format_date
     */
    public function getJpFormatDateAttribute()
    {
        $dates = $this->attributes['date'];
        $date = new \DateTime($dates);
        return $date->format('Y年m月d日 H時i分');
    }
}
