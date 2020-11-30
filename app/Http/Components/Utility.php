<?php

namespace App\Http\Components;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Http\Components\FollowService;
use App\Http\Components\ApiHandle;
use App\Management;
use App\TwitterUser;
use App\FollowsRepository;
use App\FollowerTarget;
use App\FollowTarget;
use App\UnfollowsRepository;

/**
 * フォロワーターゲットリストを作成するクラス
 */
class Utility
{
    public static function computeGapTime()
    {

    }
}
