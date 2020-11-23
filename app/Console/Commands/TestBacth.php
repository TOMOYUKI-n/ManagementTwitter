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
use App\Like;

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
        $id = 6044;
        $auto_likes = Like::where('twitter_user_id', $id)->with('keyword')->get();
        var_dump($auto_likes);
    }
}
