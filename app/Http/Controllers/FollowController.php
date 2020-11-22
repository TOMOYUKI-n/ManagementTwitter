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

class FollowController extends Controller
{
    /**
     * responce定義
     */
    public function code()
    {
        return array(
            "success" => array("status" => 200),
            "failed" => array("status" => 500)
        );
    }

    /**
     * フォローターゲット一覧を取得する
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function list()
    {
        $twitter_user_id = session()->get('twitter_id');
        Log::debug($twitter_user_id);

        try {
            $follow_target = FollowTarget::where('twitter_user_id', $twitter_user_id)
            ->whereIn('status', [1, 2, 3])->orderby('created_at', 'desc')->limit(30)
            ->with('filterWord')->get();

            return $follow_target;
        }
        catch (\Exception $e) {
            return $code["failed"];
        }
    }
}
