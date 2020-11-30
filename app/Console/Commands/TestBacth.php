<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use App\User;
use App\Keyword;
use App\TwitterUser;
use App\Management;
use App\Tweet;
use App\FollowTarget;
use App\FollowerTarget;
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

// URL
const ApiTweet = "accounts/";

/**
 * 処理動作テスト用バッチ
 */
class TestBacth extends Command
{
    protected $signature = "command:testbatch";
    protected $description = "test用";
    public function __construct() { parent::__construct(); }

    public function handle()
    {
        // 1分ごとにチェック
        for($i = 0; $i < 9; $i++){
            Log::Debug(`$i 回目`);
            $auto_tweets_list = Tweet::where('twitter_user_id', 1)->where('status', 1)->with('twitterUser')->get();
            // Log::Debug("auto_tweets_list", [$auto_tweets_list]);

            foreach ($auto_tweets_list as $auto_tweet) {
                Log::Debug('##自動ツイート開始');
                //投稿予定時刻なら自動ツイート checkSubmitDateIsNowDateの内容

                $submit_date = Carbon::create($auto_tweet->date)->format('Y-m-d H:i');
                $now_date = Carbon::now()->format('Y-m-d H:i');
                Log::debug('###現在時間: ', [$now_date]);
                Log::debug('###ツイート予定時間: ', [$submit_date]);
            
                if ($submit_date === $now_date) {
                    Log::debug("合致している");
                    $booleans = true;
                }
                else {
                    Log::debug("不一致");
                    $booleans = false;
                }

                if ($booleans) {
                    // API実行
                    // 省略
                    Log::Debug('##自動ツイート完了メール送信');
                    Log::Debug('##自動ツイート完了');
                }
            }
            // 今の分(m)と次の時間の分(m)の差(s)を算出
            $now = Carbon::now()->format('H:i');
            $next = Carbon::now()->minutes(1)->format('H:i');
            Log::Debug("now", [$now]);
            Log::Debug("next",[$next]);
            $gap = $next - $now;
            // その秒数分sleepする
            sleep($gap);
            // 再度実行
            Log::Debug('再実行');
            // $i = 9になり、次が10分目の場合は処理終了
        }
    }
}
