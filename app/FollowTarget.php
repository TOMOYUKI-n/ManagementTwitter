<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class FollowTarget extends Model
{
    protected $table = 'follow_targets';

    const STATUS = [
        1 => ['label' => '待機中'],
        2 => ['label' => 'リスト作成中'],
        3 => ['label' => 'リスト作成済']
    ];

    protected $appends = [
        'status_label'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $casts = [
        'twitter_user_id' => 'integer',
        'id' => 'integer',
        'status' => 'integer'
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
        return $this->belongsTo('App\TwitterUser');
    }

    /**
     * kwywordsテーブル
     */
    public function keyword()
    {
        return $this->belongsTo('App\Keyword', 'keyword_id');
    }


    /**
     * アクセサ status_label
     */
    public function getStatusLabelAttribute()
    {
        $status = $this->attributes['status'];
        Log::Debug($status);

        if (!isset(self::STATUS[$status])) {
            return '';
        }
        Log::Debug(self::STATUS[$status]['label']);
        return self::STATUS[$status]['label'];
    }
}
