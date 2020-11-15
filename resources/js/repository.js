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
    {twitter_id: 1000037652727401, screen_name: 'xxxhhskjosekjag', name: 'さんぷる太郎', thumbnail: '', follow: 500, follower: 3400 },
    {twitter_id: 1000037652727402, screen_name: 'dddwesekjag', name: 'てきとーまん', thumbnail: '', follow: 400, follower: 300},
    {twitter_id: 1000037652727403, screen_name: 'hapybirth', name: '誕生日', thumbnail: '', follow: 550, follower: 30},
    {twitter_id: 1000037652727404, screen_name: 'monky', name: '猿', thumbnail: '', follow: 343, follower: 340},
    {twitter_id: 1000037652727405, screen_name: 'printeee', name: '印刷業界からWEBの新生児', thumbnail: '', follow: 5, follower: 34},
    {twitter_id: 1000037652727406, screen_name: 'hogehoge', name: 'ほげさん', thumbnail: '', follow: 1100, follower: 5000},
    {twitter_id: 1000037652727407, screen_name: 'fugag3', name: 'ふがふが', thumbnail: '', follow: 11900, follower: 3000},
    // {id: 1000037652727408, screen_name: 'omo', name: 'おもしろいひと', thumbnail: ''},
    // {id: 1000037652727409, screen_name: 'ttrex', name: 'れっくす', thumbnail: ''},
    // {id: 1000037652727410, screen_name: 'logijij', name: 'じじい', thumbnail: ''},
];

/**
 * 切り替えたアカウントでtwitter APIを利用する際に取得が必要
 * twitter_users_tableの中身
 */
export const twitterUsersTable = [
    { id: 1, user_id: 1000037652727401, token: 'token_xxxhhskjosekjag', token_secret: 'token_secret_xxxhhskjosekjag' }
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
