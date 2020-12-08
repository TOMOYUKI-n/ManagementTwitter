<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GetAPI;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
    /**
     * responce定義
     */
    const CODE = [
        0 => ["status" => 200],
        1 => ["status" => 500]
    ];

    // 通常遷移
    /**
     * トップページ
     */
    public function top()
    {
        return view('top');
    }

    /**
     * 利用規約
     */
    public function term()
    {
        return view('term');
    }

    /**
     * プライバシーポリシー
     */
    public function policy()
    {
        return view('policy');
    }

    /**
     * ダッシュボード
     */
    public function dashboard() {
        return view('index.dashboard');
    }

    /**
     * ログインユーザーの情報を取得
     */
    public function show()
    {
        try {
            $users = Auth::User()->first();
            return $users;
        }
        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }
    }

}
