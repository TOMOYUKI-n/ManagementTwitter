<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Management;

class ManagerController extends Controller
{
    /**
     * responce定義
     */
    const CODE = [
        0 => ["status" => 200],
        1 => ["status" => 500]
    ];

    /**
     * サービスを稼働状態にする
     * 1:自動フォロー、2:自動アンフォロー, 3:自動いいね, 4:自動ツイート
     * @param 稼働させるサービスタイプ
     */
    public function run(Request $request)
    {
        // Log::Debug($request);
        try {
            $system_manager = Management::where('twitter_user_id', $request->twitter_id)->first();

            if ($request->type === 1) {
                $system_manager->auto_follow_status = 2;
            }
            else if ($request->type === 2) {
                $system_manager->auto_unfollow_status = 2;
            }
            else if ($request->type === 3) {
                $system_manager->auto_like_status = 2;
            }
            else if ($request->type === 4) {
                $system_manager->auto_tweet_status = 2;
            }

            $system_manager->save();
            return $system_manager;
        }
        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }
    }

    /**
     * 特定のTwitterUserが利用中の管理状態を取得
     */
    public function show(int $id)
    {
        Log::Debug('request');
        Log::Debug($id);
        
        try {
            $system_manager = Management::where('twitter_user_id', $id)->first();
            return $system_manager;
        }
        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }
    }
}
