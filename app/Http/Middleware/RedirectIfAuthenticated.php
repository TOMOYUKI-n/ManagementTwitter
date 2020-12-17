<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // ログインしていたらuser_idをセッションに登録
        if (Auth::guard($guard)->check()) {

            $id = Auth::User()->id;
            $request->session()->put('user_id', $id);
    
            Auth::logout();
            return redirect()->to('/login/twitter');

        }
        return $next($request);
    }
}
