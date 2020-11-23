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

class TwitterController extends Controller
{

    /**
     * responce定義
     */
    const CODE = [
        0 => ["status" => 200],
        1 => ["status" => 500]
    ];

    /**
     * TwitterUser 一覧と総数を返す
     * @return array
     */
    public function list()
    {
        $user_id = Auth::id();
        try {
            $accounts = TwitterUser::where('user_id', $user_id)->get();
            $accounts_num = TwitterUser::where('user_id', $user_id)->count();
            Log::Debug($accounts);
        }
        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }

        return ['accounts' => $accounts, 'accounts_num' => $accounts_num];
    }

    /**
     * TwitterUser 一覧と総数を返す test
     * @return array
     */
    public function testlist()
    {
        $user_id = Auth::id();
        try {
            $accounts = TwitterUser::where('user_id', $user_id)->get();
            $accounts_num = TwitterUser::where('user_id', $user_id)->count();
            Log::Debug($accounts);
        }
        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }

        return ['accounts' => $accounts, 'accounts_num' => $accounts_num];
    }
    
    /**
     * twitterのテストユーザ情報を取得
     * @param $id : twitter_id
     */
    public function getTestInfo(int $id)
    {
        try {
            $twitter_users_data = TwitterUser::where('id', '=', $id)->get();
            return $twitter_users_data;
        }
        catch (\Exception $e){
            return self::CODE[1]['status'];  
        }
    }

    /**
     * twitterのユーザ情報を取得
     * @param $id : twitter_id
     */
    public function getInfo(int $id)
    {
        Log::Debug($id);
        $twitter_user = TwitterUser::where('id', '=', $id)->first();
        // 存在しないユーザーを取得した場合 404
        if (is_null($twitter_user)){
            return self::CODE[1]['status'];
        }

        // ログインしているユーザーと 指定のtwitterUserアカウントのuser_idが一致しなかった場合
        // 他のユーザーの登録アカウントへのアクセスになるので 403
        $user_id = Auth::id();
        if ($user_id !== $twitter_user->user_id){
            return self::CODE[1]['status'];
        }

        // API情報のセット
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $config = config('twitter');
        $Twitter = new TwitterOAuth($config['api_key'], $config['secret_key'], $token, $token_secret);

        // API実行
        try {
            $api_request = $Twitter->get('users/show',['screen_name' => $twitter_user->screen,]);
            $twitter_users_data = [
                'name' => $api_request->name,
                'screen_name' => $api_request->screen_name,
                'thumbnail' => str_replace('_normal', '', $api_request->profile_image_url),
                'follows' => $api_request->friends_count,
                'followers' => $api_request->followers_count,
            ];
            Log::Debug($twitter_users_data);
            return $twitter_users_data;
        }
        catch (\Exception $e){
            return self::CODE[1]['status']; 
        }
    }

    /**
     * TwitterUserを削除する
     * @param twitter_id
     */
    public function delete(int $id)
    {
        $user_id = Auth::id();
        $twitter_user = TwitterUser::where('id', $id)->first();

        // ログインしているユーザーと 指定のtwitterUserアカウントのuser_idが一致しなかった場合
        // 他のユーザーの登録アカウントへのアクセスになるので 403
        if (is_null($twitter_user)) {
            return self::CODE[1]['status'];
        }

        $twitter_user->delete();
    }

}
