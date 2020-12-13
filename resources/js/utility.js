/**
 * ナビゲーションの切り替え
 */
$(function () {
    
    $('.js_push').on('click',function (){
        $('.js_toggle').toggleClass("active");
        $('.p-board__body').toggleClass("none");
    });

    $('.js_push_guest').on('click',function (){
        $('.js_toggle_guest').toggleClass("active");
        $('.p-board__body').toggleClass("none");
    });

    $(function(){
        $('.error_message').fadeIn(0.5);
        $('.flash_message__fade').fadeIn(1000);
        $('.flash_message__fade').fadeOut(3000);
    });

});
