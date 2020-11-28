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

// アカウント凍結mail class
// use App\Mail\SuspendedTwitterAccount;
use App\Mail\StopTwitterAccountMail;

// API上限時 mail class
// use App\Mail\ExceededLimit;
use App\Mail\LimitApiMail;

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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $manager = Management::find(11)->with('user')->first();
        $twitter_user = TwitterUser::find(113477987)->first();
        Log::Debug("manager", [$manager]);
        Log::Debug("twitter_user", [$twitter_user]);
        $user = $manager->user;
        Log::Debug("user", [$user]);
        Mail::to($user)->send(new CompleteAutoFollow($user, $twitter_user));

    }
}
