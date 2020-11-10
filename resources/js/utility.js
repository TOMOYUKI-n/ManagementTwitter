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


/**
 *クッキーの値を取得する
 */
export function getCookieValue (searchKey) {
    if (typeof searchKey === 'undefined') {
        return ''
    }


    let val = ''

    document.cookie.split(';').forEach(cookie => {
        const [key, value] = cookie.split('=')
        if (key === searchKey) {
            return val = value
        }
    })

    return val
}

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