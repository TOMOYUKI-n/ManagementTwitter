<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\User;
use App\TwitterUser;
use App\Tweet;

/**
 * 自動ツイート完了時に送信するメールのクラス
 */
class CompleteAutoTweet extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $twitter_user;
    public $auto_tweet;

    public function __construct(User $user, TwitterUser $twitter_user, Tweet $auto_tweet)
    {
        $this->user = $user;
        $this->twitter_user = $twitter_user;
        $this->auto_tweet = $auto_tweet;
    }

    public function build()
    {
        return $this
            ->subject('自動ツイート完了のお知らせ')
            ->view('emails.completeAutoTweet');
    }
}
