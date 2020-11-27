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
use App\FollowTarget;

class FollowController extends Controller
{
    /**
     * responce定義
     */
    const CODE = [
        0 => ["status" => 200],
        1 => ["status" => 500]
    ];

    /**
     * フォローターゲット一覧を取得する
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function list(int $id)
    {

        try {
            $follow_target = FollowTarget::where('twitter_user_id', $id)
                                // ->whereIn('status', [1, 2, 3])
                                // ->orderby('created_at', 'desc')
                                ->limit(30)
                                ->with('keyword')->get();

            return $follow_target;
        }
        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }
    }

    /**
     * 新規のフォローターゲットを追加する
     */
    public function add(Request $request)
    {
        $twitter_user_id = $request->id;

        try {
            $follow_target = new FollowTarget();
            $follow_target->twitter_user_id = $twitter_user_id;
            $follow_target->keyword_id = $request->keyword_id;
            $follow_target->account_user_name = $request->account_user_name;
            Auth::user()->followTargets()->save($follow_target);

            return self::CODE[0]['status'];
        }
        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }
    }

    /**
     * フォローターゲット情報を修正する
     * @param int twitter_user_id
     */
    public function edit(Request $request)
    {
        // Log::debug('$request ======= ');
        // Log::debug($request);
        try {
            $follow_target = FollowTarget::where('id', $request->keyword_id)->with('keyword')->first();
            if (!$follow_target) {
                abort(404);
            }
            $follow_target->keyword_id = $request->keyword_id;
            $follow_target->account_user_name = $request->account_user_name;
            $follow_target->save();
    
            return self::CODE[0]['status'];
        }
        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }
    }
    
    /**
     * フォローターゲットを削除する
     * @param int twitter_id, follow_targetのid
     */
    public function delete(Request $request)
    {

        $twitter_id = $request->twitter_user_id;
        $edit_id = $request->id;

        try {

            $follow_target = FollowTarget::where('id', $edit_id)->first();
            $status_under_making_list = 3;

            // リスト作成後であれば削除する
            if ($follow_target->status === $status_under_making_list) {
                FollowerTarget::where('twitter_user_id', $twitter_id)->delete();
                Log::Debug('削除します');
            }
            $follow_target->delete();
            return self::CODE[0]['status'];
        }
        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }

    }
}
