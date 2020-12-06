/**
 * ナビゲーションの切り替え
 */
$(function () {
    
    $('.js_push').on('click',function (){
        $('.js_toggle').toggleClass("active");
    });

    $('.js_push_guest').on('click',function (){
        $('.js_toggle_guest').toggleClass("active");
    });

    $(function(){
        $('.error_message').fadeIn(0.5);
        $('.flash_message__fade').fadeIn(1000);
        $('.flash_message__fade').fadeOut(3000);
    });

});
