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
    const UnfollowSwitchValue = 1;

    /**
     * フォロー履歴から、アクティブユーザーを検査するテーブルを作成する
     * 検査テーブルに入っているユーザーにバリデーションを行い
     * バリデーションに引っかかったユーザーをアンフォローターゲットリストにmoveする
     */
    public function handle()
    {
        Log::info('=====================================================================');
        Log::info('DetectingActiveUserBatch : 開始');
        Log::info('=====================================================================');

        //稼動中のステータスになっているauto_unfollow_statusのレコードを取得する
        $running_list = Management::where("auto_unfollow_status", Management::RUNNING)->get();

        foreach ($running_list as $item) {
            $management_id = $item->id;
            $twitter_user_id = $item->twitter_user_id;
            Log::info('#management_id : ', [$management_id]);
            Log::info('#twitter_user_id : ', [$twitter_user_id]);


            //現在のフォロワー数の確認
            $follower = Utility::getTwitterFollowerNum($management_id, $twitter_user_id);
            if (Utility::isFollowerOverEntryNumber($follower)) {
                Utility::changeAutoUnfollowStatusToStop($item);
                Log::info('フォロワー数が5000人以下です。');
                Log::info('次のユーザーにスキップします');
                continue;
            }


            // 検査テーブルにユーザーがいれば検査する
            // 検査テーブルが0の場合、検査テーブルを作成する
            $unfollow_detect_record = UnfollowDetect::where('twitter_user_id', $twitter_user_id)->first();
            if (is_null($unfollow_detect_record)) {
                self::copyFollowsRepository($twitter_user_id);
            }
            $unfollow_detect = UnfollowDetect::where('twitter_user_id', $twitter_user_id)->get();

            //アクティブユーザーバリデーション
            self::addToUnfollowTargets($management_id, $twitter_user_id, $unfollow_detect);

        }

        Log::info('=====================================================================');
        Log::info('InspectActiveUser : 終了');
        Log::info('=====================================================================');

    }

    /**
     * フォローヒストリーのユーザーを検査テーブルにコピーする
     * @param $twitter_user_id
     */
    private function copyFollowsRepository($twitter_user_id)
    {
        //フォローヒストリーをinspectにコピー
        $follow_repository = FollowsRepository::where('twitter_user_id', $twitter_user_id)
            ->select('twitter_user_id', 'twitter_id')->get()->toArray();

        data_fill($follow_repository, '*.created_at', Carbon::now()->format('Y-m-d H:i:s'));
        data_fill($follow_repository, '*.updated_at', Carbon::now()->format('Y-m-d H:i:s'));
        UnfollowDetect::insert($follow_repository);
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
        Log::info('##アクティブユーザーバリデーション開始');

        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();
        // クエリで 'id,id,id,id,id'という文字列が使用されるので、文字列を生成する
        $user_id_string_list = self::makeUsersStringList($unfollow_detect);
        foreach ($user_id_string_list as $user_id_string) {
            $api_result = (object)self::fetchActiveUserInfo($twitter_user, $user_id_string);
            $flg_skip_to_next_user = ApiHandle::handleApiError($api_result, $management_id, $twitter_user_id);
            if ($flg_skip_to_next_user === true) {
                return;
            }

            //アクティブユーザーバリデーション
            self::inspectActiveUser($api_result, $twitter_user_id);
            //検査したユーザーを検査テーブルから一括削除
            UnfollowDetect::where('twitter_user_id', $twitter_user_id)->limit(100)->delete();
        }
        Log::info('##アクティブユーザーバリデーション完了');
    }
    
    /**
     * 15日以上ツイートしていないユーザーをアンフォローターゲットDBにmoveする
     * @param $api_result
     * @param $twitter_user_id
     */
    private function inspectActiveUser($api_result, $twitter_user_id)
    {
        $before_15days = Carbon::now()->addDay(-15);
        foreach ($api_result as $detect_target) {
            $last_tweet_date = Carbon::create($detect_target->status->created_at);
            if ($last_tweet_date->lte($before_15days)) {
                self::addUnfollowTargetDB($detect_target, $twitter_user_id);
            }
        }
    }

    /**
     * unfollow_targetにユーザーを保存する
     * @param $target
     * @param $twitter_user_id
     */
    private function addUnfollowTargetDB($target, $twitter_user_id)
    {
        $unfollow_target = new UnfollowTarget();
        $unfollow_target->twitter_user_id = $twitter_user_id;
        $unfollow_target->twitter_id = $target->id_str;
        $unfollow_target->save();
    }

    /**
     * APIで検査テーブルのユーザーの情報を取得する
     * @param $twitter_user
     * @param $user_id_string
     * @return array|object
     */
    private function fetchActiveUserInfo($twitter_user, $user_id_string)
    {
        Log::info('###API ツイッターユーザーの情報取得開始');

        //APIに必要な変数の用意
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $param = [
            'user_id' => $user_id_string
        ];
        //API呼び出し
        $response_json = ApiHandle::useTwitterApi('GET', self::ApiUsersLookup,
            $param, $token, $token_secret);


        Log::info('###API ツイッターユーザーの情報取得完了');
        return $response_json;
    }


    /**
     * ['id,id,id,id,id'..., 'id,id,id,id,id...', ...]形式の文字列の配列を作成する
     * @param $users
     * @return array
     */
    private function makeUsersStringList($users)
    {
        $users_string_list = [];
        //全てのidを配列形式で取得する
        $user_id_strings = Arr::pluck($users, 'twitter_id');
        //id100件を含んだ配列をさらに新たな配列に格納する
        $users_id_strings_chunk = array_chunk($user_id_strings, 100);
        foreach ($users_id_strings_chunk as $user_id_string) {
            //id100件の配列を , カンマで接続した文字列に変換する
            $users_string_list[] = implode(',', $user_id_string);
        }

        return $users_string_list;
    }


}
