<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Management extends Model
{
    protected $table = 'managements';
    protected $casts = [
        'id' => 'integer',
        'auto_follow_status' => 'integer',
        'auto_unfollow_status' => 'integer',
        'auto_like_status' => 'integer',
        'auto_tweet_status' => 'integer'
    ];

    protected $appends = [
        'status_labels'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    /**
     * タイプを定義
     */
    const TYPE = [
        1 => 'auto_follow',
        2 => 'auto_unfollow',
        3 => 'auto_like',
        4 => 'auto_tweet',
    ];

    /**
     * ステータスを定義
     */
    const STOP = 1;
    const RUNNING = 2;
    const WAIT_API = 3;

    const STATUS = [
        1 => ['label' => 'サービス停止'],
        2 => ['label' => 'サービス稼動中'],
        3 => ['label' => 'API制限中'],
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
        return $this->belongsTo('App\TwitterUser', 'user_id');
    }

    /**
     * アクセサ
     * 各自動化サービスのステータスをラベルで返す
     */
    public function getStatusLabelsAttribute()
    {
        $status_labels = [];
        foreach (self::TYPE as $key => $service_name) {
            $status = $this->attributes[$service_name . '_status'];
            $label = self::STATUS[$status]['label'];
            $status_labels[$service_name] = $label;
        }

        return $status_labels;
    }

    /**
     * すべてのサービスを停止状態にする
     */
    public static function stopAllServicesStatus($id)
    {
        $system_manager = Management::where('id', $id)->first();
        Log::Debug('sysm', [$system_manager]);
        $system_manager->auto_follow_status = 1;
        $system_manager->auto_unfollow_status = 1;
        $system_manager->auto_like_status = 1;
        $system_manager->auto_tweet_status = 1;
        $system_manager->save();
    }

}
