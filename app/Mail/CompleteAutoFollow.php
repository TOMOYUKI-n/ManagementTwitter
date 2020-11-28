<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\User;
use App\TwitterUser;

/**
 * 自動フォロー完了時に送信するメールのクラス
 */
class CompleteAutoFollow extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $twitter_user;

    public function __construct(User $user, TwitterUser $twitter_user)
    {
        $this->user = $user;
        $this->twitter_user = $twitter_user;
    }

    public function build()
    {
        return $this
            ->subject('自動フォロー完了のお知らせ')
            ->view('emails.completeAutoFollow');
    }
}
