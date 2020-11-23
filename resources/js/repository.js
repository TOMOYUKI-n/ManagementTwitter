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
    {id: 6044, screen_name: 'tomo', name: 'tomo', thumbnail: '', follows: 500, followers: 3400 },
    {id: 12425, screen_name: 'Nayeli Stracke', name: 'Nayeli Stracke', thumbnail: '', follows: 400, followers: 300},
    {id: 59319, screen_name: 'Mrs. Meagan Mraz', name: 'Mrs. Meagan Mraz', thumbnail: '', follows: 550, followers: 30},
    {id: 63407, screen_name: 'Fabiola Feest', name: 'Fabiola Feest', thumbnail: '', follows: 343, followers: 340},
    {id: 86713, screen_name: 'Aliza Beier', name: 'Aliza Beier', thumbnail: '', follows: 5, followers: 34},
    {id: 92887, screen_name: 'Annamae Cummerata', name: 'Annamae Cummerata', thumbnail: '', follows: 1100, followers: 5000},
    {id: 711157, screen_name: 'Jaunita Upton IV', name: 'Jaunita Upton IV', thumbnail: '', follows: 11900, followers: 3000},
    {id: 5240583, screen_name: 'Dr. Milford Grant', name: 'Dr. Milford Grant', thumbnail: '', follows: 22, followers: 567},
    {id: 113477983, screen_name: 'Elody Halvorson', name: 'Elody Halvorson', thumbnail: '', follows: 33, followers: 5543},
    {id: 113477987, screen_name: 'tomozo01v', name: 'tomozo01v', thumbnail: '', follows: 112, followers: 322},
];

/**
 * 切り替えたアカウントでtwitter APIを利用する際に取得が必要
 * twitter_users_tableの中身
 */
export const twitterUsersTable = [
    { id: 4, user_id: 1, token: 'tomozo01v', token_secret: 'token_secret_xxxhhskjosekjag' }
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

/**
 * いいねリスト
 */
