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
 * TwitterAPIを実行するクラス
 * TwitterAPIがエラーになった場合のハンドリングも行う
 * Class TwitterApi
 * @package App\Http\Components
 */
class FollowService
{
    // URL
    const ApiFollowersList = 'followers/list';
    const ApiFollow = 'friendships/create';

    /**
     * フォロワーターゲットリストを作成する
     * @param $management_id
     * @param $twitter_user_id
     * @param $cursor
     */
    public static function makeFollowerTargetList($management_id, $twitter_user_id, $cursor)
    {
        Log::info('##フォロワーリスト作成');

        $waiting_status = 1;
        $under_creating_status = 2;
        $created_status = 3;

        //ターゲットアカウントリストを1件取得
        $follow_target = FollowTarget::where('twitter_user_id', $twitter_user_id)
            ->whereIn('status', [1, 2])->with('keyword')->first();
        if (empty($follow_target)) {
            return;
        }

        //リスト作成中のステータスに変更
        if ($follow_target->status === $waiting_status) {
            $follow_target->status = $under_creating_status;
            Log::debug("リスト作成中のステータスを変更");
            Log::debug($follow_target);
            $follow_target->save();
        }

        $keyword = $follow_target->keyword;
        $target_screen = $follow_target->account_user_name;
        Log::debug("keywordとtarget_screenを表示");
        Log::debug($keyword);
        Log::debug($target_screen);

        //API認証用のツイッターユーザー情報を取得
        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();
        Log::debug("API認証用のツイッターユーザー情報を取得");
        Log::debug($twitter_user);

        do {
            // APIでフォロワーのリストを取得
            $api_result = self::fetchGetFollowerListApi($twitter_user, $target_screen, $cursor);
            // エラーがあれば検索終了
            Log::debug('api_result', [$api_result]);
            $flg_skip_to_next_user = ApiHandle::handleApiError($api_result, $management_id, $twitter_user_id);
            Log::Debug("flg_skip_to_next_user", [$flg_skip_to_next_user]);
            if ($flg_skip_to_next_user === true) {
                return;
            }

            // 取得したフォロワーのリストから、フォロワーターゲットリストに追加
            self::addToFollowerTargetList($api_result, $keyword, $twitter_user_id);
            Log::debug("取得したフォロワーのリストから、フォロワーターゲットリストに追加");

            $cursor = $api_result->next_cursor_str;
            // APIのフォロワーリストで次ページがなければ終了
        } while ($cursor !== "0");

        $follow_target->status = $created_status;
        $follow_target->save();

        Log::debug('##フォロワーリスト作成完了');
    }

    /**
     * APIを使用して指定のユーザーをフォローしているユーザー一覧を取得する
     * @param $twitter_user
     * @param $target_screen
     * @param $cursor
     * @return array|object
     */
    public static function fetchGetFollowerListApi($twitter_user, $target_screen, $cursor)
    {
        Log::info('###API フォロワーリスト取得');

        //APIオプション:1~200 指定の数のフォローワー情報を取得
        $count = 200;

        //APIに必要な変数の用意
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $param = [
            'screen_name' => $target_screen,
            'count' => $count,
            'include_entities' => false,
        ];
        if (!empty($cursor)) {
            $param['cursor'] = $cursor;
        }

        Log::info("useTwitterApi パラメータ");
        Log::Debug($token);
        Log::Debug($token_secret);
        Log::Debug($param);

        //API呼び出し
        $response_json = ApiHandle::useTwitterApi('GET', self::ApiFollowersList,
            $param, $token, $token_secret);

        Log::info('###API フォロワーリスト取得完了');

        return $response_json;
    }

    /**
     * バリデーションを行ってからフォロワーターゲットリストを作成する
     * @param $api_result
     * @param $keyword
     * @param $twitter_user_id
     */
    public static function addToFollowerTargetList($api_result, $keyword, $twitter_user_id)
    {
        Log::info('####フォロワーターゲットリスト作成開始');
        Log::debug('####keyword: ', [$keyword->merged_word]);
        foreach ($api_result->users as $user) {
            $description = $user->description;
            Log::debug('####description: ', [$description]);

            //日本語プロフィールかチェック
            if (!self::isJapaneseProfile($description)) {
                Log::debug('####日本人バリデーション');
                continue;
            }
            //プロフィールが条件フィルターに該当するかチェック
            if (!self::isValidateKeyword($description, $keyword)) {
                Log::debug('####プロフィールバリデーション');
                continue;
            }
            //アンフォローリストにいないか
            if (self::isInUnfollowHistories($user, $twitter_user_id)) {
                Log::debug('####アンフォローバリデーション');
                continue;
            }
            //フォロー済リストから30日以内にフォローしてないか
            if (self::isFollowedWithin30Days($user, $twitter_user_id)) {
                Log::debug('####フォロー済バリデーション');
                continue;
            }

            //ターゲットリストに追加
            Log::debug('####フォロワーターゲットリストにユーザーを追加');
            $new_follower_target = new FollowerTarget();
            $new_follower_target->twitter_user_id = $twitter_user_id;
            $new_follower_target->twitter_id = $user->id_str;
            $new_follower_target->cursor = $api_result->next_cursor_str;
            $new_follower_target->save();
            Log::debug('####追加したユーザー: ', [$new_follower_target]);
        }
        Log::info('####フォロワーターゲットリスト作成完了');
    }


    // /**
    //  * その時点でのフォロー上限回数を取得する
    //  * 1日の上限÷1日の実行回数
    //  * @param $management_id
    //  * @param $twitter_user_id
    //  * @return int
    //  */
    // public static function getFollowLimit($management_id, $twitter_user_id)
    // {
    //     $followers = self::getTwitterFollowerNum($management_id, $twitter_user_id);

    //     //該当するフォロワー数と対応したレートを返す
    //     foreach (self::FollowLimmitPerDay as $rate => $limit) {
    //         if ((int)$rate >= (int)$followers) {
    //             return (int)($limit / self::API_PER_A_DAY);
    //         }
    //     }

    //     //上限のレートを返す
    //     return (int)(self::FOLLOW_RATE_MAX / self::API_PER_A_DAY);
    // }

    /**
     * twitterのフォロワー数を取得する
     * @param $management_id
     * @param $twitter_user_id
     * @return int
     */
    public static function getTwitterFollowerNum($management_id, $twitter_user_id)
    {
        //API認証用のツイッターユーザー情報を取得
        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();
        $api_result = ApiHandle::fetchTwitterUserInfo($twitter_user);
        $flg_skip_to_next_user = ApiHandle::handleApiError($api_result, $management_id, $twitter_user_id);
        if ($flg_skip_to_next_user === true) {
            return 0;
        }

        return $api_result->followers_count;
    }

    /**
     * APIを使ってフォローを行う
     * @param $twitter_user
     * @param $user_id
     * @return array|object
     */
    public static function fetchAutoFollow($twitter_user, $user_id)
    {
        Log::info('###API フォロー');
        //APIに必要な変数の用意
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $param = [
            'user_id' => $user_id,
        ];

        //API呼び出し
        $response_json = ApiHandle::useTwitterApi('POST', self::API_URL_FOLLOW,
            $param, $token, $token_secret);

        Log::info('###API フォロー完了');
        return $response_json;
    }

    /**
     * follow_targetsテーブルの1カラムをfollow_historiesテーブルにmoveする。
     * @param $twitter_user_id
     * @param $item
     */
    public static function moveFollowTargetsToFollowHistories($twitter_user_id, $item)
    {
        $follow_history = new FollowsRepository();
        $follow_history->twitter_user_id = $twitter_user_id;
        $follow_history->twitter_id = $item->twitter_id;
        $follow_history->save();

        $item->delete();
    }




    /**
     * 日本人のプロフィールならtrueを返す
     * @param String $description
     * @return bool
     */
    public static function isJapaneseProfile(String $description)
    {
        if (strlen($description) === mb_strlen($description, 'utf8')) {
            return false;
        }
        return true;
    }

    /**
     * 設定されたフィルター条件をクリアしていればtrueを返す
     * @param String $description
     * @param $keyword
     * @return bool
     */
    public static function isValidateKeyword(String $description, $keyword)
    {

        //除外ワードが含まれていればfalseを返す
        $removes = $keyword->remove;
        if (self::isIncludeRemove($description, $removes)) {
            Log::debug('#####除外ワードが含まれています');
            return false;
        }

        $word = $keyword->word;
        $type_and = 1;
        $type_or = 2;
        //AND検索かOR検索の条件にマッチしていればtrueを返す
        if ($keyword->type === $type_and) {
            Log::debug('#####AND条件を満たしません');
            return self::isMatchedAndFilter($description, $word);
        } elseif ($keyword->type === $type_or) {
            Log::debug('#####OR条件を満たしません');
            return self::isMatchedOrFilter($description, $word);
        }

        return false;
    }

    /**
     * アンフォロー履歴に入っているユーザならtrueを返す
     * @param $user
     * @param $twitter_user_id
     * @return bool
     */
    public static function isInUnfollowHistories($user, $twitter_user_id)
    {
        Log::Debug("UnfollowsRepository");
        Log::Debug([$user]);
        Log::Debug([$twitter_user_id]);
        $unfollow_list = UnfollowsRepository::where('twitter_user_id', $twitter_user_id)->where('twitter_id', $user->id_str)->first();
        if (is_null($unfollow_list)){
            return false;
        }
        return true;
    }

    /**
     * 30に以内にフォローしたユーザならtrueを返す
     * @param $user
     * @param $twitter_user_id
     * @return bool
     */
    public static function isFollowedWithin30Days($user, $twitter_user_id)
    {
        $before_30days = Carbon::now()->addDay(-30);
        Log::Debug($before_30days);
        $follow_list = FollowsRepository::where('twitter_user_id', $twitter_user_id)
            ->where('twitter_id', $user->id_str)
            ->whereDate('created_at', '>', $before_30days)->first();

        if (is_null($follow_list)){
            return false;
        }
        return true;

    }

    /**
     * 文字列内に除外ワードが含まれていればtrueを返す
     * @param $description
     * @param $removes
     * @return bool
     */
    public static function isIncludeRemove($description, $removes)
    {
        if (empty($removes)) {
            return false;
        }
        $remove_list = explode(' ', $removes);
        foreach ($remove_list as $remove) {
            if (strpos($description, $remove) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * 文字列内に全ての条件ワードが入っていればtrueを返す
     * @param $description
     * @param $word
     * @return bool
     */
    public static function isMatchedAndFilter($description, $word)
    {
        $and_word_list = explode(' ', $word);
        foreach ($and_word_list as $and_word) {
            if (strpos($description, $and_word) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * 文字列内にいずれかの条件ワードが入っていればtrueを返す
     * @param $description
     * @param $word
     * @return bool
     */
    public static function isMatchedOrFilter($description, $word)
    {
        $or_word_list = explode(' ', $word);
        foreach ($or_word_list as $or_word) {
            if (strpos($description, $or_word) !== false) {
                return true;
            }
        }
        return false;
    }
}