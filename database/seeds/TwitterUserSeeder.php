<?php

use Illuminate\Database\Seeder;

class TwitterUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\TwitterUser::class, 9)->create();
    }
}
