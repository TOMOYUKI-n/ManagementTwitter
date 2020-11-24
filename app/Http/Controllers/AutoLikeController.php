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

    /**
     * 新規いいねの設定を追加する
     */
    public function add(Request $request)
    {
        try {
            $like = new Like();
            $like->twitter_user_id = $request->id;
            $like->keyword_id = $request->keyword_id;
    
            Auth::user()->likes()->save($like);
            return self::CODE[0]['status'];
        }
        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }
    }

    /**
     * 自動いいね設定情報を変更する
     */
    public function edit(Request $request)
    {
        try{
            $likes = Like::where('id', $request->id)->with('keyword')->first();

            $likes->keyword_id = $request->keyword_id;
            $likes->save();
            return self::CODE[0]['status'];
        }
        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }
    }

    /**
     * 自動いいね設定を削除する
     */
    public function delete(Request $request)
    {
        try{
            $likes = Like::where('id', $request->id)->first();
            $likes->delete();
            return self::CODE[0]['status'];
        }
        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }
    }
}
