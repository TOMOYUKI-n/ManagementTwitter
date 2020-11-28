<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnfollowsRepositoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unfollows_repository', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('twitter_user_id');
            $table->string('twitter_id', 30);
            $table->timestamps();

            $table->foreign('twitter_user_id')->references('id')->on('twitter_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unfollows_repository');
    }
}
