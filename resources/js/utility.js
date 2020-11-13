$(function () {
    
    $('.js_push').on('click',function (){
        $('.js_toggle').toggleClass("active");
    });

    $(function(){
        $('.error_message').fadeIn(0.5);
        $('.flash_message__fade').fadeIn(1000);
        $('.flash_message__fade').fadeOut(3000);
    });

});

export const linkParam = [
    {id: 1, label: 'アカウント登録'},
    {id: 2, label: '自動フォロー'},
    {id: 3, label: '自動アンフォロー'},
    {id: 4, label: '自動いいね'},
    {id: 5, label: '自動予約ツイート'},
    {id: 6, label: 'キーワード登録'},
];

/**
 *Vueでエラーを判別するためのステータスコード
 */
export const OK = 200
export const CREATED = 201
export const UNAUTHORISED = 401
export const NOT_FOUND = 404
export const EXPIRED = 419
export const UNPROCESSABLE_ENTRY = 422
export const INTERNAL_SERVER_ERROR = 500