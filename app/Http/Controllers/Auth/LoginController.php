<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\User;
use App\TwitterUser;
use App\Management;
use Exception;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/dashboard';
    protected function redirectTo()
    {
        session()->flash('flash_message', __('Login!'));
        return '/dashboard';
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        Log::Debug('aaaaaaaaaaaaaaaa');
        $this->middleware('guest')->except('logout');
    }

    /**
     * OAuth認証先にリダイレクト
     *
     * @param str $provider
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }


    /**
     * OAuth認証の結果受け取り
     *
     * @param str $provider
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {

        Log::Debug("login");
        //twitterからユーザー情報を取得する
        try {
            $providerUser = \Socialite::with($provider)->user();
            //アクセストークン取得
            $twitter_id = $providerUser->id;
            $token = $providerUser->token;
            $tokenSecret = $providerUser->tokenSecret;

        }
        catch (\Exception $e) {
            return redirect('/login')->with('error_message', '予期せぬエラーが発生しました');
        }

        //すでに登録されたデータが有れば取得
        $data = session()->all();
        $user_id = $data['user_id'];
        Log::Debug($user_id);
        $twitter_user = DB::table('twitter_users')->where('token', $token)->first();

        // 登録データが有る場合、sessionに格納してリダイレクト
        if (!is_null($twitter_user)) {
            if ($user_id === $twitter_user->user_id) {
                session()->put('twitter_id', $twitter_user->id);
            }
            $user = User::find($user_id);
            Log::Info('登録データが有る場合、sessionに格納してリダイレクト');
            Auth::login($user);
            return redirect('/dashboard')->with('flash_message', '既に登録されているアカウントです。');
        }

        /**
         * 新登録の場合TwitterUserデータをDBに保存してリダイレクト
         * managementに登録（twitterアカウントを登録することで初めてマネジメントで管理する）
         */
        try {
            Log::Debug('TwitterUser登録開始');

            $twitter_user = [
                'user_id' => $user_id,
                'token' => $token,
                'token_secret' => $tokenSecret,
                'screen' => $providerUser->nickname,
            ];
            $new_twitter_user = TwitterUser::create($twitter_user);

            Log::Debug('managementに登録開始');
            $manager = new Management();
            $manager->user_id = $user_id;
            $manager->twitter_user_id = $new_twitter_user->id;
            $manager->save();
            session()->put('twitter_id', $new_twitter_user->id);

            Log::Debug('登録終了');
            // ログインする
            $user = User::find($user_id);
            Auth::login($user);
            return redirect('/dashboard')->with('flash_message', 'ログインしました');
        }
        catch (\Exception $e) {
            return redirect('/dashboard')->with('flash_message', '予期せぬエラーが発生しました。再度登録しなおしてください');
        }
    }

}
