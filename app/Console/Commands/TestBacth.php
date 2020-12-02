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
        $twitter_user_id = 1;
        Log::info("検査テーブルにユーザーがいれば検査する");
        $unfollow_detect_record = UnfollowDetect::where('twitter_user_id', $twitter_user_id)->first();
        Log::info([$unfollow_detect_record]);

        if (is_null($unfollow_detect_record)) {
            $follow_repository = FollowsRepository::where('twitter_user_id', $twitter_user_id)
                ->select('twitter_user_id', 'twitter_id')->get()->toArray();

            data_fill($follow_repository, '*.created_at', Carbon::now()->format('Y-m-d H:i:s'));
            data_fill($follow_repository, '*.updated_at', Carbon::now()->format('Y-m-d H:i:s'));
            UnfollowDetect::insert($follow_repository);
        }

        $unfollow_detect = UnfollowDetect::where('twitter_user_id', $twitter_user_id)->get();
        Log::info([$unfollow_detect]);
    }
}
