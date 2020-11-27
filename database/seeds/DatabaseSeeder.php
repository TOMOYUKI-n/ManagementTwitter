<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(twitterUserSeeder::class);
        $this->call(ManagementSeeder::class);
        $this->call(LikesSeeder::class);
    }
}
