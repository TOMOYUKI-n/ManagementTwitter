<?php

namespace App\Console\Commands;

use Illuminate\Support\Arr;
use App\Http\Components\Utility;
use Illuminate\Support\Facades\Log;
use App\Http\Components\ApiHandle;
use Illuminate\Console\Command;
use App\Management;
use App\TwitterUser;
use App\UnfollowTarget;
use App\FollowsRepository;
use Carbon\Carbon;

class DetectNoFollowBackBatch extends Command
{

    protected $signature = 'command:detect_no_followback';
    protected $description = 'detect no followback user';

    public function __construct()
    {
        parent::__construct();
    }

    // url
    const ApiFriendshipLookup = 'friendships/lookup';
    const UnfollowSwitchValue = 5000;

    /**
     * フォローから7日経過したユーザーデータを元に、
     * APIを使用してフォローリレーションを取得する
     * フォローバックされていないユーザーは、アンフォローターゲットリストに保存する
     */
    public function handle()
    {
        Log::info('=====================================================================');
        Log::info('DetectingFollowback : 開始');
        Log::info('=====================================================================');

        //稼動中のステータスになっているauto_unfollow_statusのレコードを取得する
        $running_list = Management::where("auto_unfollow_status", Management::RUNNING)->get();

        foreach ($running_list as $item) {
            $management_id = $item->id;
            $twitter_user_id = $item->twitter_user_id;
            Log::info('#management_id : ', [$management_id]);
            Log::info('#twitter_user_id : ', [$twitter_user_id]);


            //現在フォロワー数の確認
            $follower = Utility::getTwitterFollowerNum($management_id, $twitter_user_id);
            if (Utility::isFollowerOverEntryNumber($follower)) {
                Log::debug("フォロワー数が一定の数値に達していません");
                Utility::changeAutoUnfollowStatusToStop($item);
                continue;
            }

            // フォローから７日経過したユーザーの取得
            $followed_7days_pass_user = self::getUsersFollowed7daysAgo($twitter_user_id);
            // フォローバックバリデーション
            self::addToUnfollowTargetsByCheckFollowback($management_id, $twitter_user_id, $followed_7days_pass_user);

        }

        Log::info('=====================================================================');
        Log::info('DetectingFollowback : 終了');
        Log::info('=====================================================================');
    }

    /**
     * フォローから7日経過したユーザー一覧の取得
     * @param $twitter_user_id
     * @return mixed
     */
    private function getUsersFollowed7daysAgo($twitter_user_id)
    {
        $the_day_7_before = Carbon::today()->addDay(-7);
        $users = FollowsRepository::where('twitter_user_id', $twitter_user_id)
            ->whereDate('created_at',  '=' , $the_day_7_before)->get();
        log::Debug("フォローから7日経過したユーザー");
        Log::Debug([$users]);
        return $users;
    }

    /**
     *
     * @param $management_id
     * @param $twitter_user_id
     * @param $users
     */
    private function addToUnfollowTargetsByCheckFollowback($management_id, $twitter_user_id, $users)
    {
        Log::info('##フォローバックバリデーション開始');
        //API認証用のツイッターユーザー情報を取得
        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();
        $user_id_string_list = Utility::makeUsersStringList($users);
        foreach ($user_id_string_list as $user_id_string){

            // apiは配列型 -> オブジェクトに変換
            $api_result = (object)self::fetchFollowbackInfo($twitter_user, $user_id_string);
            $flg_skip_to_next_user = ApiHandle::handleApiError($api_result, $management_id, $twitter_user_id);
            if ($flg_skip_to_next_user === true) {
                return;
            }

            $this->DetectingFollowback($api_result, $twitter_user_id);
        }

        Log::info('##フォローバックバリデーション完了');
    }

    /**
     * APIを使ってフォローリレーション情報の取得を行う
     * @param $twitter_user
     * @param $user_id_string
     * @return array|object
     */
    private function fetchFollowbackInfo($twitter_user, $user_id_string)
    {
        Log::info('###API フォローリレーションの取得開始');

        //APIに必要な変数の用意
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $param = [
            'user_id' => $user_id_string
        ];
        //API呼び出し
        $response_json = ApiHandle::useTwitterApi('GET', self::ApiFriendshipLookup,
            $param, $token, $token_secret);


        Log::info('###API フォローリレーションの取得完了');
        return $response_json;
    }

    /**
     * フォローバックしていないユーザーをアンフォローターゲットリストに追加する
     * @param $api_result
     * @param $twitter_user_id
     */
    private function DetectingFollowback($api_result, $twitter_user_id)
    {
        foreach ($api_result as $detect_target){
            if (!in_array('followed_by', $detect_target->connections)){
                Utility::addUnfollowTargetDB($detect_target, $twitter_user_id);
            }
        }
    }

}
