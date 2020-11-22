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

/**
 * twitterログイン
 */
Route::get('/login/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('/login/{provider}/callback', 'Auth\LoginController@handleProviderCallback');

/**
 * 認証が必要なルート
 */
Route::group(['middleware' => 'auth'], function() {
    
    /**
     * ダッシュボード 画面遷移
     */
    Route::get('/dashboard', 'IndexController@dashboard')->name('index.dashBoard');

    /**
     * 登録済みtwitterアカウントの一覧取得
     */
    Route::get('/test/twitter/users/list', 'TwitterController@testlist')->name('twitter.testlist');
    Route::get('/test/twitter/users/getTestInfo/{id}', 'TwitterController@getTestInfo')->name('twitter.testinfo');
    Route::get('/api/twitter/users/list', 'TwitterController@list')->name('twitter.list');
    Route::get('/api/twitter/users/{id}', 'TwitterController@getInfo')->name('twitter.info');
    Route::delete('/api/twitter/users/{id}', 'TwitterController@delete')->name('twitter.delete');

    /**
     * キーワード関連
     */
    Route::post('/api/keyword', 'KeywordController@add')->name('keyword.add');
    Route::get('/api/keyword', 'KeywordController@show')->name('keyword.show');
    Route::get('/api/keyword/{id}', 'KeywordController@get')->name('keyword.get');
    Route::put('/api/keyword/{id}', 'KeywordController@edit')->name('keyword.edit');
    Route::delete('/api/keyword/{id}', 'KeywordController@delete')->name('keyword.delete');

    /**
     * 自動フォロー関連
     */
    Route::get('/api/follow/list', 'FollowController@list')->name('follow.list');




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