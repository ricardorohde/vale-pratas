$(function () {
    $('.main_nav_mobile_menu').click(function () {
        $('.main_nav ul').slideToggle();
    });

    $('body').on('click', '#product-thumb img', function () {
        var thumb = $(this);
        var img = $("#product-img");
        var src = thumb.attr('src');
        var separa_img = src.split('&');
        var separa_img2 = separa_img[0].split('src=');
        var separa_img3 = separa_img2[1];
        src = '/' + separa_img3;
        img.attr('src', src);
        return false;
    });
});