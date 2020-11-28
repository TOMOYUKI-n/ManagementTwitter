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
use App\Mail\SuspendedTwitterAccount;
use App\Mail\ExceededLimit;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Http\Components\ApiHandle;
use App\Http\Components\FollowService;

class TestBacth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:testbatch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test用';

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

    // フォロワー数に応じた一日のフォロー上限数 FOLLOW_RATE_PER_DAY
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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $manager = Management::find($management_id)->with('user')->first();
        $twitter_user = TwitterUser::find($twitter_user_id)->first();
        $user = $manager->user;
        Mail::to($user)->send(new CompleteFollow($user, $twitter_user));

    }
}
