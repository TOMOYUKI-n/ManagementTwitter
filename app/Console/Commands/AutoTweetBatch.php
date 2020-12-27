<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use App\Tweet;
use App\Management;
use App\TwitterUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\CompleteAutoTweet;
use App\Http\Components\ApiHandle;

class AutoTweetBatch extends Command
{

    protected $signature = 'command:auto_tweet';
    protected $description = 'auto tweet at bokked Date';

    public function __construct()
    {
        parent::__construct();
    }

    // URL
    const TweetApi = 'statuses/update';
    /**
     * 予約投稿を行う
     * herokuのスケジュール10分に1回実行に合わせる.
     * 
     * 設定されている自動ツイートを全て取得する
     * 取得した自動ツイートの、ツイート予定時間がYYYY-MM-DD HH:MMの形式で一致した場合
     * 現在のツイートだと認識して自動ツイートを行う
     */
    public function handle()
    {
        Log::Debug('=====================================================================');
        Log::Debug('AutoTweet : 開始');
        Log::Debug('=====================================================================');

        // 1分ごとにチェック
        for($i = 0; $i < 10; $i++){
            Log::Debug("AutoTweet:".$i."分目実行");
            // auto_tweet_statusが稼動中のステータスになっているレコードを取得する
            $running_list = Management::where("auto_tweet_status", Management::RUNNING)->get();

            foreach ($running_list as $item) {
                $management_id = $item->id;
                $twitter_user_id =  $item->twitter_user_id;
                Log::Debug('#management_id : ', [$management_id]);
                Log::Debug('#twitter_user_id : ' , [$twitter_user_id]);

                //ユーザーごとの自動ツイート配列を取得する
                $auto_tweets_list = Tweet::where('twitter_user_id', $twitter_user_id)->where('status', 1)->with('twitterUser')->get();
                
                foreach ($auto_tweets_list as $auto_tweet) {
                    Log::Debug('##自動ツイート開始');
                    // 投稿予定時刻なら自動ツイート
                    if (self::checkSubmitDateIsNowDate($auto_tweet)) {
                        //API実行
                        $api_result = self::fetchTweetApi($auto_tweet);
                        //APIエラーの場合の処理と判定
                        $flg_skip_to_next_user = ApiHandle::handleApiError($api_result, $management_id, $twitter_user_id);
                        if ($flg_skip_to_next_user === true) {
                            break;
                        }
                        //ツイート完了のメールを送信
                        Log::Debug('##自動ツイート完了メール送信');
                        self::sendMail($management_id, $twitter_user_id, $auto_tweet);
                        self::changeStatusTweeted($auto_tweet);
                        Log::Debug('##自動ツイート完了');
                    }
                }
            }
            // $i = 9になり、次が10分目の場合は処理終了
            if($i === 9){
                sleep(0);
            }else{
                sleep(60);
            }
        }
        Log::Debug('=====================================================================');
        Log::Debug('AutoTweet : 終了');
        Log::Debug('=====================================================================');
    }

    /**
     * 自動ツイート完了のメールを送信する
     * @param $management_id
     * @param $twitter_user_id
     * @param $auto_tweet
     */
    private function sendMail($management_id, $twitter_user_id, $auto_tweet)
    {
        $system_manager = Management::where('id', $management_id)->with('user')->first();
        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();
        $user = $system_manager->user;
        Mail::to($user)->send(new CompleteAutoTweet($user, $twitter_user, $auto_tweet));
    }

    /**
     * automatic_tweetのステータスをツイート済みに変更する
     * @param $auto_tweet
     */
    private function changeStatusTweeted($auto_tweet)
    {
        $auto_tweet->status = 2; //ツイート済み
        $auto_tweet->save();
    }


    /**
     * APIを使用して自動ツイートを行う
     * @param $auto_tweet
     * @return array|object
     */
    private function fetchTweetApi($auto_tweet)
    {
        Log::Debug('###API 自動ツイート開始');
        Log::debug('##ツイート内容: ', [$auto_tweet->tweet]);
        //APIに必要な変数の用意
        $token = $auto_tweet->twitterUser->token;
        $token_secret = $auto_tweet->twitterUser->token_secret;
        $param = [
            'status' => $auto_tweet->tweet,
        ];

        //API呼び出し
        $response_json = ApiHandle::useTwitterApi('POST', self::TweetApi,
            $param, $token, $token_secret);

        Log::Debug('###API 自動ツイート完了');
        return $response_json;
    }


    /**
     * 現在時間と、ツイート予定時間を分レベルで比較して、同時刻の場合はtrueを返す
     * @param $auto_tweet
     * @return bool
     */
    private function checkSubmitDateIsNowDate($auto_tweet)
    {
        $submit_date = Carbon::create($auto_tweet->date)->format('Y-m-d H:i');
        $now_date = Carbon::now()->format('Y-m-d H:i');
        Log::debug('###現在時間: ', [$now_date]);
        Log::debug('###ツイート予定時間: ', [$submit_date]);

        if ($submit_date === $now_date) {
            return true;
        }
        return false;
    }
}
