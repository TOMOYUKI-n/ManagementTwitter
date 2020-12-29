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
use App\Tweet;
use Carbon\Carbon;

class AutoTweetController extends Controller
{
    /**
     * responce定義
     */
    const CODE = [
        0 => ["status" => 200],
        1 => ["status" => 500],
        2 => ["status" => 400],
    ];

    /**
     * 登録した自動ツイート一覧を取得する
     */
    public function show(int $id)
    {
        try {
            $twitter_user_id = $id;

            $before_days = Carbon::now()->addDay(-7);
            $tweets = Tweet::where('twitter_user_id', $twitter_user_id)
                                ->whereDate('date', '>', $before_days)
                                ->orderBy('date')
                                ->get();

            $tweets_num = Tweet::where('twitter_user_id', $twitter_user_id)
                                ->whereDate('date', '>', $before_days)
                                ->orderBy('date')
                                ->count();
            return array($tweets, $tweets_num);
        }

        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }
    }

    /**
     * 新規ツイートを登録する
     */
    public function add(int $id, Request $request)
    {
        $d = $request->date;
        $t = $request->time;
        $s = ' ';
        $twitter_user_id = $id;
        $date_time = $d.$s.$t;
        $user_id = Auth::id();

        // 5分後かどうかチェック
        $resultTime = self::checkTime($date_time);
        if ($resultTime) {
            try {
                $tweet = new Tweet();
                $tweet->user_id = $user_id;
                $tweet->twitter_user_id = $twitter_user_id;
                $tweet->tweet = $request->tweet;
                $tweet->date = $date_time;
                $tweet->save();
    
                return self::CODE[0]['status'];
            }
            catch (\Exception $e) {
                return self::CODE[1]['status'];
            }
        } else {
            return self::CODE[2]['status'];
        }

    }

    /**
     * 自動ツイート情報を変更する
     */
    public function edit(int $id, Request $request)
    {

        $twitter_user_id = $id;
        $d = $request->date;
        $t = $request->time;
        $s = ' ';
        $date_time = $d.$s.$t;

        $resultTime = self::checkTime($date_time);
        if ($resultTime) {
            try{
                $tweet = Tweet::where('id', $request->id)->first();
                $tweet->tweet = $request->tweet;
                $tweet->date = $date_time;
                $tweet->save();
    
                return self::CODE[0]['status'];
            }
            catch (\Exception $e) {
                return self::CODE[1]['status'];
            }
        } else {
            return self::CODE[2]['status'];
        }

    }

    /**
     * ツイートを削除する
     */
    public function delete(int $id)
    {

        try{
            $tweet = Tweet::where('id', $id)->first();
            $tweet->delete();
            return self::CODE[0]['status'];
        }
        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }
    }

    /**
     * 5分後チェック
     */
    public function checkTime(string $date) {
        $inputValue = new Carbon($date);
        $afterFiveTime = Carbon::now()->addMinute(5);
        return $inputValue->gt($afterFiveTime)? true:false;
    }

}
