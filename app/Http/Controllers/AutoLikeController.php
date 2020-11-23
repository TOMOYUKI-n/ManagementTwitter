<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\User;
use App\TwitterUser;
use App\Like;

class AutoLikeController extends Controller
{
    /**
     * responce定義
     */
    const CODE = [
        0 => ["status" => 200],
        1 => ["status" => 500]
    ];

    /**
     * 登録した自動いいね設定一覧を取得
     */
    public function show(int $id)
    {
        try {
            $likes = Like::where('twitter_user_id', $id)->with('keyword')->get();
            return $likes;
        }
        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }
    }
}
