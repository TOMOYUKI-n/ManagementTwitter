/**
 * ナビゲーションの切り替え
 */
$(function () {
    
    $('.js_push-sidebar').on('click',function (){
        $('.js_open-sidebar').toggleClass("active");
        $('.js_open-navi').toggleClass("active");

        $('.p-board__body').toggleClass("none");
        $('.p-policy__main-container').toggleClass("none");
        $('.l-main__contact').toggleClass("none");
        $('.l-main__top--wrapper').toggleClass("none");
        $('.l-main__container').toggleClass("none");
        // $('.p-board__sp--top').toggleClass("none");
    });

    $('.js_push_guest').on('click',function (){
        $('.js_toggle_guest').toggleClass("active");
        
        $('.p-board__body').toggleClass("none");
        $('.p-policy__main-container').toggleClass("none");
        $('.l-main__contact').toggleClass("none");
        $('.l-main__top--wrapper').toggleClass("none");
        $('.l-main__container').toggleClass("none");
        // $('.p-board__sp--top').toggleClass("none");
    });

    $(function(){
        $('.error_message').fadeIn(0.5);
        $('.flash_message__fade').fadeIn(1000);
        $('.flash_message__fade').fadeOut(3000);
    });

});
