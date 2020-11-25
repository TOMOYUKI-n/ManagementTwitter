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
     * 自動フォロー関連 ターゲットアカウント
     */
    Route::get('/api/follow/list/{id}', 'FollowController@list')->name('follow.list');
    Route::post('/api/follow/{id}', 'FollowController@add')->name('follow.add');
    Route::put('/api/follow/{id}', 'FollowController@edit')->name('follow.edit');
    Route::post('/api/follow/delete/{id}', 'FollowController@delete')->name('follow.delete');


    /**
     * システム管理関連
     */
    Route::get('/api/system/status/{id}', 'ManagerController@show')->name('system.show');
    Route::post('/api/system/running', 'ManagerController@run')->name('system.run');
    Route::post('/api/system/stop', 'ManagerController@stop')->name('system.stop');


    /**
     * いいね機能関連
     */
    Route::get('/api/like/list/{id}','AutoLikeController@show')->name('like.show');
    Route::post('/api/like/{id}', 'AutoLikeController@add')->name('like.add');
    Route::put('/api/like/{id}','AutoLikeController@edit')->name('like.edit');
    Route::post('/api/like/delete/{id}','AutoLikeController@delete')->name('like.delete');

    /**
     * 予約ツイート関連
     */
    Route::get('/api/tweet/list/{id}', 'AutoTweetController@show')->name('tweet.show');
    Route::post('/api/tweet/{id}', 'AutoTweetController@add')->name('tweet.add');
    Route::post('/api/tweet/edit/{id}', 'AutoTweetController@edit')->name('tweet.edit');
    Route::delete('/api/tweet/{id}', 'AutoTweetController@delete')->name('tweet.delete');

});