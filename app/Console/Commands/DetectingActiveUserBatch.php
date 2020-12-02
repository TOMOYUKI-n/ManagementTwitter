<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Components\Utility;
use Illuminate\Support\Facades\Log;
use App\Http\Components\ApiHandle;
use App\UnfollowsRepository;
use App\Management;
use App\TwitterUser;
use App\UnfollowTarget;
use App\UnfollowDetect;
use App\FollowsRepository;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class DetectingActiveUserBatch extends Command
{

    protected $signature = 'command:detecting_active_user';
    protected $description = 'detect active user';
    public function __construct()
    {
        parent::__construct();
    }

    // url
    const ApiUsersLookup = 'users/lookup';
    const UnfollowSwitchValue = 5000;

    /**
     * フォロー履歴から、アクティブユーザーを検査するテーブルを作成する
     * 検査テーブルに入っているユーザーにバリデーションを行い
     * バリデーションに引っかかったユーザーをアンフォローターゲットリストにmoveする
     */
    public function handle()
    {
        Log::Debug('=====================================================================');
        Log::Debug('DetectingActiveUserBatch : 開始');
        Log::Debug('=====================================================================');

        //稼動中のステータスになっているauto_unfollow_statusのレコードを取得する
        $running_list = Management::where("auto_unfollow_status", Management::RUNNING)->get();

        foreach ($running_list as $item) {
            $management_id = $item->id;
            $twitter_user_id = $item->twitter_user_id;
            Log::Debug('#management_id : ', [$management_id]);
            Log::Debug('#twitter_user_id : ', [$twitter_user_id]);


            //現在のフォロワー数の確認
            $follower = Utility::getTwitterFollowerNum($management_id, $twitter_user_id);
            if (Utility::isFollowerOverEntryNumber($follower)) {
                Utility::changeAutoUnfollowStatusToStop($item);
                Log::Debug('フォロワー数が5000人以下です。');
                Log::Debug('次のユーザーにスキップします');
                continue;
            }


            // 検査テーブルにユーザーがいれば検査する
            // 検査テーブルが0の場合、検査テーブルを作成する
            Log::Debug("検査テーブルにユーザーがいれば検査する");
            $unfollow_detect_record = UnfollowDetect::where('twitter_user_id', $twitter_user_id)->first();
            Log::Debug([$unfollow_detect_record]);

            /**
             * フォロー済ユーザーを検査テーブルにコピーする
             */
            if (is_null($unfollow_detect_record)) {
                $follow_repository = FollowsRepository::where('twitter_user_id', $twitter_user_id)
                    ->select('twitter_user_id', 'twitter_id')->get()->toArray();
    
                data_fill($follow_repository, '*.created_at', Carbon::now()->format('Y-m-d H:i:s'));
                data_fill($follow_repository, '*.updated_at', Carbon::now()->format('Y-m-d H:i:s'));
                UnfollowDetect::insert($follow_repository);
            }
            $unfollow_detect = UnfollowDetect::where('twitter_user_id', $twitter_user_id)->get();
            Log::Debug([$unfollow_detect]);

            //アクティブユーザーバリデーション
            self::addToUnfollowTargets($management_id, $twitter_user_id, $unfollow_detect);

        }

        Log::Debug('=====================================================================');
        Log::Debug('DetectingActiveUserBatch : 終了');
        Log::Debug('=====================================================================');

    }

    /**
     * APIで検査テーブルのユーザーの最新ツイート情報を取得する
     * ツイートされた日にちからアクティブユーザーかどうかを判別して、
     * 非アクティブユーザーならアンフォローターゲットリストにmoveする
     * @param $management_id
     * @param $twitter_user_id
     * @param $unfollow_detect
     */
    private function addToUnfollowTargets($management_id, $twitter_user_id, $unfollow_detect)
    {
        Log::Debug('##アクティブユーザーバリデーション開始');

        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();

        // クエリで 'id,id,id,id,id'という文字列が使用されるので、文字列を生成する
        $user_id_list = self::makeUsersStringList($unfollow_detect);

        foreach ($user_id_list as $user_id_string) {
            $api_result = (object)self::fetchActiveUserInfo($twitter_user, $user_id_string);
            $flg_skip_to_next_user = ApiHandle::handleApiError($api_result, $management_id, $twitter_user_id);
            if ($flg_skip_to_next_user === true) {
                return;
            }

            //アクティブユーザーバリデーション
            self::detectActiveUser($api_result, $twitter_user_id);
            //検査したユーザーを検査テーブルから一括削除
            UnfollowDetect::where('twitter_user_id', $twitter_user_id)->limit(100)->delete();
        }
        Log::Debug('##アクティブユーザーバリデーション完了');
    }
    
    /**
     * 15日以上ツイートしていないユーザーをアンフォローターゲットDBにmoveする
     * @param $api_result
     * @param $twitter_user_id
     */
    private function detectActiveUser($api_result, $twitter_user_id)
    {
        Log::Debug("アクティブユーザーバリデーション");
        $before_15days = Carbon::now()->addDay(-15);

        foreach ($api_result as $detect_target) {
            $last_tweet_date = Carbon::create($detect_target->status->created_at);

            // 15日以下かどうか
            if ($last_tweet_date->lte($before_15days)) {
                Utility::addUnfollowTargetDB($detect_target, $twitter_user_id);
            }
        }
    }

    /**
     * APIで検査テーブルのユーザーの情報を取得する
     * @param $twitter_user
     * @param $user_id_string
     * @return array|object
     */
    private function fetchActiveUserInfo($twitter_user, $user_id_string)
    {
        Log::Debug('###API ツイッターユーザーの情報取得開始');

        //APIに必要な変数の用意
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $param = [
            'user_id' => $user_id_string
        ];
        //API呼び出し
        $response_json = ApiHandle::useTwitterApi('GET', self::ApiUsersLookup,
            $param, $token, $token_secret);


        Log::Debug('###API ツイッターユーザーの情報取得完了');
        return $response_json;
    }
}
