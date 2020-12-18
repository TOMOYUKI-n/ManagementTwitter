<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use App\User;
use App\Keyword;
use App\TwitterUser;
use App\Management;
use App\Tweet;
use App\Like;
use App\FollowTarget;
use App\FollowerTarget;
use App\UnfollowDetect;
use App\FollowsRepository;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use App\Mail\CompleteAutoFollow;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Http\Components\ApiHandle;
use App\Http\Components\FollowService;
use App\Mail\StopTwitterAccountMail;
use App\Mail\LimitApiMail;
use App\Http\Components\Utility;
use App\Mail\CompleteAutoTweet;
// URL
const ApiTweet = "accounts/";

/**
 * 処理動作テスト用バッチ
 */
class TestBacth extends Command
{
    protected $signature = "command:testbatch";
    protected $description = "test用";
    public function __construct() { 
        parent::__construct();
    }

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
                }
            }
            // $i = 9になり、次が10分目の場合は処理終了
            if($i === 9){
                sleep(0);
            }else{
                sleep(1);
            }
        }
        Log::Debug('=====================================================================');
        Log::Debug('AutoTweet : 終了');
        Log::Debug('=====================================================================');
    }
}
