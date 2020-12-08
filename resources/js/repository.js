// UI作成時のテストデータ

export const linkParam = [
    {id: 1, label: 'アカウント登録'},
    {id: 2, label: '自動フォロー'},
    {id: 3, label: '自動アンフォロー'},
    {id: 4, label: '自動いいね'},
    {id: 5, label: '自動予約ツイート'},
    {id: 6, label: 'キーワード登録'},
];

/**
 * Authで取得する情報
 * user
 */
export const loginUserInfo = [
    { id: 1, name: 'tomo' }
];

/**
 * twitterのアカウント情報
 * twitterAPIレスポンス結果
 */
export const twitterAccount = [
    {id: 1, screen_name: 'cryptodev14', name: 'cryptodev14', thumbnail: '', follows: 500, followers: 3400 },
    {id: 2, screen_name: 'tomozo01v', name: 'tomozo01v', thumbnail: '', follows: 400, followers: 300},
];

/**
 * 切り替えたアカウントでtwitter APIを利用する際に取得が必要
 * twitter_users_tableの中身
 */
export const twitterUsersTable = [
    { id: 1, user_id: 1, token: 'test', token_secret: 'token_secret_xxxhhskjosekjag' }
];

/**
 * キーワード登録内容 (フォロワーサーチといいねで使用)
 * filter_wordsの中身
 */
export const filterWords = [
    { id:1, word: 'フリーランス JavaScript Laravel フリーランス JavaScript Laravel' ,type: 'AND'},
    { id:2, word: '事業経営 WEB プログラミング' ,type: 'AND' },
    { id:3, word: '個人事業 個人経営' ,type: 'OR' },
    { id:4, word: 'webライター' ,type: 'NOT' }
];

/**
 * ターゲットアカウントリスト情報
 * target_accountsの中身
 */
export const targetAccountList = [
    { id: 1, user_id: 1, twitter_user_id: 1000037652727401, status: 1, target: 'pokepokehoihoi', filter_word: filterWords[0] },
    { id: 2, user_id: 1, twitter_user_id: 1000037652727401, status: 2, target: 'インフルえんさーA', filter_word: filterWords[1] },
    { id: 3, user_id: 1, twitter_user_id: 1000037652727402, status: 3, target: 'じじ', filter_word: filterWords[2] },
    { id: 4, user_id: 1, twitter_user_id: 1000037652727402, status: 1, target: 'じじ', filter_word: filterWords[3] }
];

/**
 * 自動化サービスの実施状況
 * serviceManagersの中身
 * defalult 1 false 停止 : 0 true 実行
 */
export const manegementServiceStatus = [
    {
        id: 1, user_id: 1, twitter_user_id: 1000037652727401,
        auto_follow_status: 1, auto_unfollow_status: 1, auto_like_status: 1, auto_tweet_status: 1,
    },
    {
        id: 2, user_id: 1, twitter_user_id: 1000037652727402,
        auto_follow_status: 0, auto_unfollow_status: 0, auto_like_status: 0, auto_tweet_status: 0,
    }
];

