$(document).ready(function(){
    //轮播
    $('.carousel').carousel({
        interval: 3000
    });

    $('#page-content .box').hover(function(){
        $('.box .img_detail').animate({left:'-150px'},500);
    },function(){
        $('.box .img_detail').animate({left:'0px'},500);
    });

    $('#flash_sale .mask').click(function(){
        window.location.href = SITE_URL + 'index/search?flash_sale=true&page_size=9';
    });

    $('#hot_sale .mask').click(function(){
        window.location.href = SITE_URL + 'index/search?hot_sale=true&page_size=9';
    });

    $('#hot_change .mask').click(function(){
        window.location.href = SITE_URL + 'index/search?hot_exchange=true&page_size=9';
    });
});