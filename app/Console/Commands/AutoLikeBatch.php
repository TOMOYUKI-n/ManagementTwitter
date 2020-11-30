<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Like;
use App\Management;
use App\TwitterUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\CompleteAutoTweet;
use App\Http\Components\ApiHandle;

class AutoLikeBatch extends Command
{

    protected $signature = 'command:auto_like';
    protected $description = 'Auto favorite';

    public function __construct()
    {
        parent::__construct();
    }

    // url
    const ApiSearch = 'search/tweets';//API_URL_SEARCH
    const ApiFavorites = 'favorites/create';

    // API上限 900/1日
    const ApiRatePerDay = 240; //API_REQUEST_RATE_PER_DAY
    const ActiveApiPerDay = 24; //DO_API_PER_A_DAY
    const Interval_hours = 1; //INTERVAL_HOURS

    /**
     * 登録された自動いいねのフィルターワードごとにツイート検索を行う.
     * 検索にヒットしたツイートに対していいねを行う
     * API実行回数を設定して上限回数に達しないように.
     */
    public function handle()
    {
        Log::info('=====================================================================');
        Log::info('AutoLike : 開始');
        Log::info('=====================================================================');

        // auto_follow_statusが稼動中のステータスになっているレコードを取得する
        $running_list = Management::where('auto_like_status', Management::RUNNING)->get();

        foreach ($running_list as $item) {
            $management_id = $item->id;
            $twitter_user_id = $item->twitter_user_id;
            Log::info('#management_id : ', [$management_id]);
            Log::info('#twitter_user_id : ' , [$twitter_user_id]);

            //ユーザーごとのいいね条件配列を取得
            $auto_like_list = Like::where('twitter_user_id', $twitter_user_id)
                ->with('twitterUser', 'keyword')->get();

            $auto_like_list_quantity = count($auto_like_list);

            //いいね条件ごとに検索
            foreach ($auto_like_list as $auto_like) {
                $flg_skip_to_next_user = false;

                //検索にヒットしたツイート配列を取得
                $api_result = self::fetchGetTweetListApi($auto_like, $auto_like_list_quantity);
                $flg_skip_to_next_user = ApiHandle::handleApiError($api_result, $management_id, $twitter_user_id);
                if ($flg_skip_to_next_user === true) {
                    Log::notice('#APIエラーのため次のユーザーにスキップ');
                    break;
                }
                //取得したツイート一覧に対していいねをする
                foreach ($api_result->statuses as $item) {
                    $like_target_id = $item->id_str;
                    $api_result = self::fetchLikeApi($auto_like, $like_target_id);
                    $flg_skip_to_next_user = ApiHandle::handleApiError($api_result, $management_id, $twitter_user_id);
                    if ($flg_skip_to_next_user === true) {
                        Log::notice('#APIエラーのため次のユーザーにスキップ');
                        break 2;
                    }

                }

            }
        }

        Log::info('=====================================================================');
        Log::info('AutoLike : 終了');
        Log::info('=====================================================================');

    }


    /**
     * APIを使用して、フィルターワードで指定されたワードでツイート検索を行う
     * @param $auto_like
     * @param $auto_like_list_quantity
     * @return array|object
     */
    private function fetchGetTweetListApi($auto_like, $auto_like_list_quantity)
    {
        Log::info('##API ツイートリスト取得開始');

        //APIに必要な変数の用意
        $count = self::ApiRatePerDay / self::ActiveApiPerDay / $auto_like_list_quantity;
        $query = $auto_like->keyword->getMergedWordStringForQuery();
        Log::info('##いいねする数: ', [$count]);
        Log::info('##検索クエリ: ', [$query]);

        $token = $auto_like->twitterUser->token;
        $token_secret = $auto_like->twitterUser->token_secret;
        $param = [
            'q' => $query,
            'count' => $count,
            'result_type' => 'recent',
            'include_entities' => false,
        ];

        //API呼び出し
        $response_json = ApiHandle::useTwitterApi('GET', self::ApiSearch,
            $param, $token, $token_secret);

        Log::info('##API ツイートリスト取得完了');

        return $response_json;
    }

    /**
     * APIを使用して、いいねをする
     * @param $auto_like
     * @param $like_target_id
     * @return array|object
     */
    private function fetchLikeApi($auto_like, $like_target_id)
    {
        Log::debug('##API 自動いいね開始');

        //APIに必要な変数の用意
        $token = $auto_like->twitterUser->token;
        $token_secret = $auto_like->twitterUser->token_secret;

        $param = [
            'id' => $like_target_id,
            'include_entities' => false,
        ];

        //API呼び出し
        $response_json = ApiHandle::useTwitterApi('POST', self::ApiFavorites,
            $param, $token, $token_secret);

        Log::debug('##API 自動いいね完了');
        return $response_json;
    }
}
