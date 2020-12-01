<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Components\ApiHandle;
use App\Http\Components\Utility;
use App\UnfollowsRepository;
use App\Management;
use App\TwitterUser;
use App\UnfollowTarget;

class AutoUnfollowBatch extends Command
{

    protected $signature = 'command:auto_unfollow';
    protected $description = 'Auto unFollowing';

    public function __construct()
    {
        parent::__construct();
    }

    const ApiUnfollow = 'friendships/destroy';
    const UnfollowSwitchValue = 1;
    const Interval_hours = 1;
    // Api上限
    const ApiRatePerDay = 24 / self::Interval_hours;
    const UnfollowMaxRate = 150;

    /**
     * heroku 1日ごとに実行
     * フォローワー数が5000人以上いる時に実行する
     * アンフォローターゲットリストのユーザーをアンフォローしてアンフォロー履歴に保存する
     * @return mixed
     */
    public function handle()
    {
        Log::Debug('=====================================================================');
        Log::Debug('AutoUnfollow : 開始');
        Log::Debug('=====================================================================');

        // auto_unfollow_statusが稼動中のステータスになっているレコードを取得する
        $running_list = Management::where("auto_unfollow_status", Management::RUNNING)->get();

        foreach ($running_list as $item) {
            $management_id = $item->id;
            $twitter_user_id = $item->twitter_user_id;
            Log::Debug('#management_id : ', [$management_id]);
            Log::Debug('#twitter_user_id : ' , [$twitter_user_id]);

            //現在のフォロワー数の確認
            $follower = Utility::getTwitterFollowerNum($management_id, $twitter_user_id);
            if (Utility::isFollowerOverEntryNumber($follower)) {
                Utility::changeAutoUnfollowStatusToStop($item);
                Log::Debug('フォロワー数が5000人以下です。');
                Log::Debug('次のユーザーにスキップします');
                continue;
            }

            $unfollow_targets = UnfollowTarget::where('twitter_user_id', $twitter_user_id)->get();
            //アンフォロー実行
            self::autoUnfollow($management_id, $twitter_user_id, $unfollow_targets);

            Log::Debug('=====================================================================');
            Log::Debug('AutoUnfollow : 終了');
            Log::Debug('=====================================================================');
        }
    }

    /**
     * アンフォローターゲットリストのユーザーをアンフォローする
     * アンフォロー後に、アンフォローターゲットをアンフォロー履歴にmoveする
     * @param $management_id
     * @param $twitter_user_id
     * @param $unfollow_targets
     */
    private function autoUnfollow($management_id, $twitter_user_id, $unfollow_targets)
    {
        Log::Debug('##自動アンフォロー開始');

        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();
        $unfollow_count = 0;
        $unfollow_limit = (int)(self::UnfollowMaxRate / self::ApiRatePerDay);
        Log::debug('アンフォロー上限回数: ', [$unfollow_limit]);

        // アンフォローの人数分繰り返す
        foreach ($unfollow_targets as $unfollow_target) {
            $api_result = (object)self::fetchAutoUnfollow($twitter_user, $unfollow_target->twitter_id);
            $flg_skip_to_next_user = ApiHandle::handleApiError($api_result, $management_id, $twitter_user_id);
            if ($flg_skip_to_next_user === true) {
                return;
            }

            self::moveToRepository($twitter_user_id, $unfollow_target);

            $unfollow_count++;
            if ($unfollow_count >= $unfollow_limit){
                return;
            }
        }

        Log::Debug('##自動アンフォロー完了');

    }

    /**
     * アンフォローターゲットをアンフォロー履歴にmoveする
     * @param $twitter_user_id
     * @param $unfollow_target
     */
    private function moveToRepository($twitter_user_id, $unfollow_target)
    {
        $unfollows = new UnfollowsRepository();
        $unfollows->twitter_user_id = $twitter_user_id;
        $unfollows->twitter_id = $unfollow_target->twitter_id;
        $unfollows->save();

        $unfollow_target->delete();
    }


    /**
     * APIを使用してアンフォローする
     * @param $twitter_user
     * @param $user_id
     * @return array|object
     */
    private function fetchAutoUnfollow($twitter_user, $user_id)
    {
        Log::info('###API 自動アンフォロー開始');
        //APIに必要な変数の用意
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $param = [
            'user_id' => $user_id,
        ];

        //API呼び出し
        $response_json = ApiHandle::useTwitterApi('POST', self::ApiUnfollow,
            $param, $token, $token_secret);


        Log::info('###API 自動アンフォロー完了');
        return $response_json;
    }

}
