<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Components\ApiHandle;
use App\Http\Components\FollowService;
use App\Management;
use App\TwitterUser;
use App\FollowsRepository;
use App\FollowerTarget;
use App\Mail\CompleteAutoFollow;
use App\Mail\StopTwitterAccountMail;
use App\Mail\LimitApiMail;

class AutoFollowBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:auto_follow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto follow';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    // API URL
    const ApiFollowersList = 'followers/list';
    const ApiFollow = 'friendships/create';

    // フォロー回数を決めるのに使用
    const IntervalHours = 2;
    const ApiPerDay = 24 / self::IntervalHours;

    // フォロワー数に応じた一日のフォロー上限数
    const FollowLimmitPerDay = [
        "100" => 20,
        "500" => 24,
        "1000" => 40,
        "1500" => 50,
        "2000" => 50,
        "3000" => 50,
    ];
    // フォロー上限MAX
    const FollowLimitMax = 50;

    /**
     * 自動フォローを行う
     * フォロワーターゲットリスト用の処理は量が多いため別ファイルに記述.
     * 
     * フォロー後にフォロワーターゲットリストからフォローヒストリーにデータをコピーする。
     */
    public function handle()
    {
        Log::Debug("=====================================================================");
        Log::Debug("AutoFollow 開始");
        Log::Debug("=====================================================================");

        //auto_follow_statusが稼動中のステータスになっているレコードを取得する
        $running_list = Management::where("auto_follow_status", Management::RUNNING)->get();
        
        foreach ($running_list as $item) {
            $management_id = $item->id;
            $twitter_user_id = $item->twitter_user_id;
            Log::Debug("management_id : ", [$management_id]);
            Log::Debug("twitter_user_id : ", [$twitter_user_id]);

            /**
             * ターゲットリストの作成
             */
            // 最後に作成されたフォロワーターゲットリストのcursorカラムを見て
            // フォロワーターゲットリストを作る必要があるかないかを判定する
            $follower_target_list_latest = FollowerTarget::where("twitter_user_id", $twitter_user_id)->latest()->first();

            if (!empty($follower_target_list_latest)) {
                $follower_cursor = $follower_target_list_latest->cursor;
            }
            else {
                $follower_cursor = null;
            }
            Log::Debug("cursor : ", [$follower_cursor]);

            // フォロワーターゲットリストが未作成、作成途中の場合はリストを作成する
            if (is_null($follower_cursor) || $follower_cursor !== "0") {
                FollowService::makeFollowerTargetList($management_id, $twitter_user_id, $follower_cursor);
            }

            /**
             * ここから自動フォロー処理
             */
            $follower_target_list = FollowerTarget::where("twitter_user_id", $twitter_user_id)->with("twitterUser")->get();

            // フォロワーターゲットリストがない場合は次のユーザーへスキップ
            if (is_null($follower_target_list)) {
                Log::Debug("フォロワーターゲットリスト：0件 のため次のユーザーに移行");
                continue;
            }
            // フォローを行う
            self::autoFollow($management_id, $twitter_user_id, $follower_target_list);
        }
        Log::Debug("=====================================================================");
        Log::Debug("AutoFollow 終了");
        Log::Debug("=====================================================================");
    }


    /**
     * フォローレート上限回数まで、自動フォローを行う
     * @param $management_id :管理id
     * @param $twitter_user_id :登録アカウントのtwitter_id
     * @param $follower_target_list : フォロワーターゲットリスト
     */
    private function autoFollow($management_id, $twitter_user_id, $follower_target_list)
    {
        Log::Debug("フォロー開始 =========");
        // API認証用のツイッターユーザー情報を取得
        $twitter_user = TwitterUser::where("id", $twitter_user_id)->first();

        $counter = 0;
        // フォローレートの決定
        $follow_limit = self::getFollowLimit($management_id, $twitter_user_id);
        Log::Debug("follow_limit", [$follow_limit]);

        foreach ($follower_target_list as $item) {
            // APIでフォロー
            $api_follow_result = self::fetchAutoFollow($twitter_user, $item->twitter_id);

            $flg_skip_to_next_user = ApiHandle::handleApiError($api_follow_result, $management_id, $twitter_user_id);
            if ($flg_skip_to_next_user === true) {
                Log::Debug("APIエラー発生のためフォロー中止");
                return;
            }

            self::moveFollowTargetsToFollowsRepository($twitter_user_id, $item);

            $counter++;
            //レート上限を超えたら終了
            if ($counter >= $follow_limit) {
                Log::Debug("レート上限です");
                break;
            }
        }

        // 全てのフォロワーターゲットをフォローした時点で自動フォロー完了
        $target_quantity = FollowerTarget::where("twitter_user_id", $twitter_user_id)->count();
        if($target_quantity === 0){
            Log::Debug("フォローワーターゲットのフォローが完了しました");
            self::sendMail($management_id, $twitter_user_id);
        }
        Log::Debug("フォロー完了 =========");
    }

    // TODO: メール設定
    /**
     * 自動フォロー完了メールを送信する
     * @param $management_id
     * @param $twitter_user_id
     */
    private function sendMail($management_id, $twitter_user_id)
    {
        $manager = Management::where('id', $management_id)->with('user')->first();
        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();
        $user = $manager->user;
        Mail::to($user)->send(new CompleteAutoFollow($user, $twitter_user));
    }


    /**
     * follow_targetsテーブルの1カラムをfollows_repositoryテーブルにコピーする。
     * follow_targetsテーブル
     * @param $twitter_user_id
     * @param $follower_target_item
     */
    private function moveFollowTargetsToFollowsRepository($twitter_user_id, $follower_target_item)
    {
        $follow_item = new FollowsRepository();
        $follow_item->twitter_user_id = $twitter_user_id;
        $follow_item->twitter_id = $follower_target_item->twitter_id;
        $follow_item->save();

        $follower_target_item->delete();
    }


    /**
     * APIを使ってフォローを行う
     * @param $twitter_user　 :[{"App\\TwitterUser":{"id":0000000000}}] 自身のid
     * @param $user_id : ["999999999"] フォロー対象のid 
     * @return array|object
     */
    private function fetchAutoFollow($twitter_user, $user_id)
    {
        Log::Debug('###API フォロー');
        Log::Debug("twitter_user", [$twitter_user]);
        Log::Debug("user_id", [$user_id]);
        // APIに必要な変数の用意
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $param = [
            'user_id' => $user_id,
        ];

        //API呼び出し
        $response_json = ApiHandle::useTwitterApi('POST', self::ApiFollow,
            $param, $token, $token_secret);

        Log::Debug('###API フォロー完了');
        return $response_json;
    }


    /**
     * その時点でのフォロー上限回数を取得する
     * 1日の上限と1日の実行回数
     * @param $management_id
     * @param $twitter_user_id
     * @return int follow_limit [2] 
     */
    public function getFollowLimit($management_id, $twitter_user_id)
    {
        // フォロワー数を取得
        $followers = self::getTwitterFollowerNum($management_id, $twitter_user_id);

        //該当するフォロワー数と対応したレートを返す
        foreach (self::FollowLimmitPerDay as $rate => $limit) {
            if ((int)$rate >= (int)$followers) {
                return (int)($limit / self::ApiPerDay);
            }
        }

        // 上限のレートを返す
        return (int)(self::FollowLimitMax / self::ApiPerDay);
    }


    /**
     * twitterのフォロワー数を取得する
     * @param $management_id
     * @param $twitter_user_id [{"App\\TwitterUser":{"id": 自身のtwitter id}}]
     * @return int
     */
    private function getTwitterFollowerNum($management_id, $twitter_user_id)
    {
        // API認証用のツイッターユーザー情報を取得
        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();

        // バッチで利用する自身のツイッターユーザー情報を取得
        $api_result = ApiHandle::fetchTwitterUserInfo($twitter_user);

        $flg_skip_to_next_user = ApiHandle::handleApiError($api_result, $management_id, $twitter_user_id);
        if ($flg_skip_to_next_user === true) {
            return 0;
        }

        return $api_result->followers_count;
    }

}
