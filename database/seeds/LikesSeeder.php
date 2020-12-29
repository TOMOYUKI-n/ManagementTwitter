<?php

use Illuminate\Database\Seeder;
use App\Like;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class LikesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        $ids = DB::table('twitter_users')->select('id')->where('user_id', '=', 1)->get();

        // 最低1つだけセット
        DB::table('likes')->insert([
            [
                'user_id' => 1,
                'twitter_user_id' => $ids[0]->id,
                'keyword_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);

    }
}
