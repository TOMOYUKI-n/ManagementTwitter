<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

// トップ
Route::get('/', 'IndexController@top')->name('top');
// 利用規約への遷移
Route::get('/term', 'IndexController@term')->name('term');
// ポリシーへの遷移
Route::get('/policy', 'IndexController@policy')->name('policy');
// ログイン失敗時
Route::get('/misslogin', 'IndexController@misslogin')->name('misslogin');
Route::get('/home', 'HomeController@index')->name('home');
// お問い合わせへの遷移
Route::get('/contact', 'ContactController@index')->name('contact.index');
Route::post('/contact/confirm', 'ContactController@confirm')->name('contact.confirm');
Route::post('/contact/complete', 'ContactController@send')->name('contact.send');
// ログイン失敗時
Route::get('/misslogin', 'IndexController@misslogin')->name('misslogin');
//ソーシャルログイン
Route::get('/login/{provider}', 'Auth\LoginController@redirectToProvider');
//ソーシャルログインのcallback
Route::get('/login/{provider}/callback', 'Auth\LoginController@handleProviderCallback');


// 他のルートに該当しない場合indexを返す
// Route::get('{any?}', function () {
//     return view('index');
// })->where('any', '.+');

Route::group(['middleware' => 'auth'], function() {
    // news一覧用
    Route::get('/dashboard', 'IndexController@dashboard')->name('index.dashBoard');
    // アカウント登録画面遷移
    Route::get('/account', 'IndexController@account')->name('index.account');

    // アカウント登録処理
    Route::post('/provider/connect', 'ConnectController@connect');
    // アカウント解除処理
    Route::post('/provider/disconnect', 'ConnectController@disconnect');
    // アカウント情報取得処理
    Route::get('/provider/status', 'ConnectController@status');

    // アカウント一覧画面遷移
    Route::get('/accountList', function(){ return view('index.accountList'); });
    // フォローチェック
    Route::post('/accountList/followcheck', 'AccountController@followCheck');
    // フォロー用
    Route::post('/accountList/follows', 'AccountController@follows');
    // ユーザー情報取得用
    Route::get('/auth/users', 'AccountController@getUsers');
    // ユーザーフォロー情報取得用
    Route::get('/auth/following', 'AccountController@getAuthFollowData');

    // 自動フォロー用
    Route::post('/accountList/autofollows', 'AccountController@autoFollows');
});