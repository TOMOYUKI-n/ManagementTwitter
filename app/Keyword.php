<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * 検索キーワードに使用
 */
class Keyword extends Model
{
    protected $table = 'keywords';

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'type' => 'integer'
    ];
    
    /**
     * タイプを定義
     */
    const AND = 1;
    const OR = 2;
    const TYPE = [
        1 => ['label' => '次のワードを含む'],
        2 => ['label' => 'いずれかのワードを含む'],
    ];

    protected $appends = [
        'type_label', 'merged_word'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    /**
     * リレーションシップ - usersテーブル
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * リレーションシップ - automatic_likesテーブル
     */
    public function likes()
    {
        return $this->hasMany('App\Like', 'keyword_id');
    }

    /**
     * アクセサ - type_label
     * @return string
     */
    public function getTypeLabelAttribute()
    {
        $type = $this->attributes['type'];

        if (!isset(self::TYPE[$type])) {
            return '';
        }

        return self::TYPE[$type]['label'];
    }

    /**
     * 登録されたワードと除外ワードと1つの文字列にした形式で返す
     * アクセサ - merged_word
     * @return string
     */
    public function getMergedWordAttribute()
    {
        $type = $this->attributes['type'];
        if (!isset(self::TYPE[$type])) {
            return '';
        }
        $type_string = self::TYPE[$type]['label'];

        $word = $this->attributes['word'];
        $remove = $this->attributes['remove'];

        return "$type_string ： [$word] 、 除外ワード：[$remove]";
    }


    /**
     * 登録されたワードと除外ワードを
     * TwitterAPIでサーチするためのの文字列にして返す
     * @return string
     */
    public function getMergedWordStringForQuery()
    {
        $type = $this->attributes['type'];
        $word = $this->attributes['word'];
        $str_word = ((int)$type === self::OR) ? str_replace(" ", " OR ", $word) : $word;
        $remove = (!empty($remove)) ? $this->generateRemoveString($this->attributes['remove']) : "";

        //　OR @存在しないSCREEN で検索文字が含まれているユーザー名のツイートを省く
        // -filter:retweetsでリツイートを省く
        // lang:jaで日本語のツイート以外を省く
        return $str_word . $remove . ' OR @z_zz__zz1928 -filter:retweets lang:ja';
    }

    /**
     * 除外ワードを -文字列 -文字列 -文字列の形に変換して返す
     * @param $word
     * @return string
     */
    private function generateRemoveString($word)
    {
        $exploded_words = explode(" ", $word);
        $remove_string = '';
        foreach ($exploded_words as $exploded_word) {
            $remove_string = $remove_string . ' -"' . $exploded_word . '"';
        }
        return $remove_string;
    }
}
