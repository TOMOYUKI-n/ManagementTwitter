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

        $auto_like_list = Like::where('twitter_user_id', 1)->with('twitterUser', 'keyword')->get();
        Log::Debug([$auto_like_list]);
    }
}
