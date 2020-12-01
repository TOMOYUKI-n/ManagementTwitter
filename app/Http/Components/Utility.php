<?php

namespace App\Http\Components;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Components\ApiHandle;
use App\Management;
use App\TwitterUser;

/**
 * unfollowとdetectでの共通処理
 */
class Utility
{
    const UnfollowSwitchValue = 1;

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
}
