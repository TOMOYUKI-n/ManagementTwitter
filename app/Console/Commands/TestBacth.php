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
use App\Management;
use App\Tweet;

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
        // $tweets = Tweet::where('twitter_user_id', 6044)->whereDate('date', '>', '2020-11-18 00:16:49')->orderBy('date')->get();
        // $tweet_num = Tweet::where('twitter_user_id', 6044)->whereDate('date', '>', '2020-11-18 00:16:49')->orderBy('date')->count();
        // Log::Debug($tweet_num);
        // var_dump($tweets);

        // $d = '2020-11-18 00:16:00';
        // $date = new \DateTime($d);
        
        // $able = $date->format('Y-m-d H:i');
        // Log::debug($able);
        $tweet = new Tweet();
        $tweet->user_id = 1;
        $tweet->twitter_user_id = 6044;
        $tweet->tweet = 'サクナヒメからお米がどれだけつくるのが大変なのかわかった気がする';
        $tweet->date = '2020-11-30 00:10';
        $tweet->save();
    }
}
