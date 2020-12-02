<?php

namespace App\Http\Components;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Components\ApiHandle;
use App\Management;
use App\TwitterUser;
use App\UnfollowTarget;
use Illuminate\Support\Arr;

/**
 * unfollowとdetectでの共通処理
 */
class Utility
{
    const UnfollowSwitchValue = 5000;

    /**
     * APIを使用してツイッターのフォロワー数を取得する
     * @param $system_manager_id
     * @param $twitter_user_id
     * @return int
     */
    public static function getTwitterFollowerNum($management_id, $twitter_user_id)
    {
        //API認証用のツイッターユーザー情報を取得
        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();
        $api_result = ApiHandle::fetchTwitterUserInfo($twitter_user);
        $flg_skip_to_next_user = ApiHandle::handleApiError($api_result, $management_id, $twitter_user_id);
        if ($flg_skip_to_next_user === true) {
            return 0;
        }

        return $api_result->followers_count;
    }

    /**
     * フォロワー数が稼動条件数を満たしていればtrueを返す
     * @param $follower
     * @return bool
     */
    public static function isFollowerOverEntryNumber($follower)
    {
        if ($follower > self::UnfollowSwitchValue) {
            return false;
        }
        return true;
    }

    /**
     * SystemManagerのauto_unfollow_statusを停止状態にする
     * @param $manager
     */
    public static function changeAutoUnfollowStatusToStop($manager)
    {
        $manager->auto_unfollow_status = Management::STOP;
        $manager->save();
    }

    /**
     * ['id,id,id,id,id'..., 'id,id,id,id,id...', ...]形式の文字列の配列を作成する
     * @param $users
     * @return array
     */
    public static function makeUsersStringList($users)
    {
        $users_string_list = [];

        //全てのidを配列形式で取得する
        Log::Debug("全てのidを配列形式で取得する");
        $user_id_strings = Arr::pluck($users, 'twitter_id');

        //id100件を含んだ配列をさらに新たな配列に格納する
        $users_id_strings_chunk = array_chunk($user_id_strings, 100);

        Log::Debug("users_id_strings_chunk");
        Log::Debug([$users_id_strings_chunk]);
        foreach ($users_id_strings_chunk as $user_id_string) {
            //id100件の配列を , カンマで接続した文字列に変換する
            $users_string_list[] = implode(',', $user_id_string);
        }

        return $users_string_list;
    }

    /**
     * unfollow_targetにユーザーを保存する
     * @param $target
     * @param $twitter_user_id
     */
    public static function addUnfollowTargetDB($target, $twitter_user_id)
    {
        Log::Debug("UnfollowTargetDB に追加実行");
        $unfollow_target = new UnfollowTarget();
        $unfollow_target->twitter_user_id = $twitter_user_id;
        $unfollow_target->twitter_id = $target->id_str;
        $unfollow_target->save();
    }
}
