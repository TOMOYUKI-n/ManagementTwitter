<?php

namespace App\Http\Controllers;

use App\User;
use App\Keyword;
use App\TwitterUser;
use App\FollowTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KeywordController extends Controller
{

    /**
     * responce定義
     */
    const CODE = [
        0 => ["status" => 200],
        1 => ["status" => 500],
        2 => ["status" => 404],
    ];

    /**
     * 入力文字列の全角スペースを半角スペースに変換する
     * 半角スペースの重複を排除する
     */
    private function fixWordSpace($words)
    {
        // 全角スペースを半角スペースに変換する
        $clearWord = mb_convert_kana($words, 's', 'UTF-8');
        // 重複した半角スペースを1つに置換する
        $clearWord = preg_replace('/\s+/', ' ', $clearWord);
        // 先頭が半角スペースの場合、削除する
        if (' ' === mb_substr($clearWord, 0, 1))
        {
            $clearWord = mb_substr($clearWord, 1);
        }
        return $clearWord;
    }

    /**
     * 新規フィルターワードを登録する
     */
    public function add(Request $request)
    {
        try {
            $keyword = new Keyword();
            $keyword->type = $request->type;
            $keyword->word = $this->fixWordSpace($request->word);
            $keyword->remove = $this->fixWordSpace($request->remove);
            $keyword->remove = $request->remove;
    
            Auth::user()->keywords()->save($keyword);
            return self::CODE[0]['status'];
        }
        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }
    }

    /**
     * キーワード一覧を取得
     */
    public function show()
    {
        try {
            $keywordList = Auth::user()->keywords()->get();
            return $keywordList;
        }
        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }
    }

    /**
     * キーワードを削除
     */
    public function delete(int $id){
        try {
            $deleteKeywords = Auth::user()->keywords()->where('id', $id)->first();
            $deleteKeywords->delete();
            return self::CODE[0]['status'];
        }
        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }
    }

    /**
     * キーワードを変更
     */
    public function edit(int $id, Request $request)
    {

        /**
         * リスト作成中、リスト作成済みのキーワードを変更されると表示データの整合性が取れない
         * そのためfollow_targetsテーブルで使用されているkeyword_idの場合は変更せずエラーを返す
         */
        $check_exist = FollowTarget::where('keyword_id', $id)->whereIn('status', [3])->exists();
        Log::Debug("キーワードidを使用しているリストの確認");
        if($check_exist){
            return self::CODE[2]['status'];
        }
        else {
            try {
                Log::Debug("キーワードidを使用していないのでキーワードを変更");
                $update = Auth::user()->keywords()->where('id', $id)->first();
                $update->type = $request->type;
                $update->word = $request->word;
                $update->remove = $request->remove;
                $update->save();
                return self::CODE[0]['status'];
            }
            catch (\Exception $e) {
                return self::CODE[1]['status'];
            }
        }
    }

    /**
     * 指定したキーワードを1つだけ取得
     */
    public function get(int $id)
    {
        try {
            $getWord = Auth::user()->keywords()->where('id', $id)->first();
            return $getWord;
        }
        catch (\Exception $e) {
            return self::CODE[1]['status'];
        }

    }

}
