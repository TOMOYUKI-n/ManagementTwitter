<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\User;
use App\TwitterUser;

/**
 * API上限エラー検知時に送信するメールのクラス
 */
class LimitApiMail extends Mailable
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
            ->subject('API利用制限のお知らせ')
            ->view('emails.limited');
    }
}
