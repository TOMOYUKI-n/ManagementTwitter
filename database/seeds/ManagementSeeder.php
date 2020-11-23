<?php

use Illuminate\Database\Seeder;
use App\Management;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        $twitter_users_num = DB::table('twitter_users')->select('id')->count();
        $ids = DB::table('twitter_users')->select('id')->where('user_id', '=', 1)->get();

        for($i = 0; $i < $twitter_users_num; $i++){
            DB::table('managements')->insert([
                [
                    'user_id' => 1,
                    'twitter_user_id' => $ids[$i]->id,
                    'auto_follow_status' => 1,
                    'auto_unfollow_status' => 1,
                    'auto_like_status' => 1,
                    'auto_tweet_status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            ]);
        }
    }
}
