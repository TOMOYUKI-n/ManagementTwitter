<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\User;
use Carbon\Carbon;
use App\Keyword;
use App\TwitterUser;

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

        $twitter_user = TwitterUser::where('id', 4)->first();
        // API情報のセット
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $config = config('twitter');
        $Twitter = new TwitterOAuth($config['api_key'], $config['secret_key'], $token, $token_secret);
        Log::Debug('aaaaa');

        $api_request = $Twitter->get('users/show',['screen_name' => $twitter_user->screen,]);
        var_dump($api_request);
        Log::Debug('bbbbb');
        $twitter_users_data = [
            'name' => $api_request->name,
            'screen_name' => $api_request->screen_name,
            'thumbnail' => str_replace('_normal', '', $api_request->profile_image_url),
            'follows' => $api_request->friends_count,
            'followers' => $api_request->followers_count,
        ];
        Log::Debug($twitter_users_data);

    }
}
