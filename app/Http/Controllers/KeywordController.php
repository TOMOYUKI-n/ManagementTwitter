<?php

namespace App\Http\Controllers;

use App\User;
use App\Keyword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\TwitterUser;

class KeywordController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

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

        $twitter_user_id = 1;

        $keyword = new Keyword();
        // $keyword->type = $request->type;
        $keyword->word = $this->fixWordSpace($request->word);
        // $keyword->word = $request->word;
        $keyword->remove = $this->fixWordSpace($request->remove);
        $keyword->remove = $request->remove;

        Auth::user()->keywords()->save($keyword);
        $new = Keyword::where('id', $keyword->id)->with('user')->first();

        return $new;
    }

    /**
     * キーワード一覧を取得
     */
    public function show()
    {
        $keywordList = Auth::user()->keywords()->get();
        return $keywordList;
    }

    /**
     * キーワードを削除
     */
    public function delete(int $id){
        $deleteKeywords = Auth::user()->keywords()->where('id', $id)->first();
        if (! $deleteKeywords){
            abort(404);// Not Foundページを表示
        }
        $deleteKeywords->delete();
    }

    /**
     * キーワードを変更
     */
    public function edit(int $id, Request $request)
    {
        $update = Auth::user()->keywords()->where('id', $id)->first();
        if (! $update){
            abort(404);// Not Foundページを表示
        }
        $update->type = $request->type;
        $update->word = $request->word;
        $update->remove = $request->remove;
        $update->save();
        return $update;
    }

    /**
     * 指定したキーワードを1つだけ取得
     */
    public function get(int $id)
    {
        $getWord = Auth::user()->keywords()->where('id', $id)->first();
        return $getWord ?? abort(404);
    }

}
