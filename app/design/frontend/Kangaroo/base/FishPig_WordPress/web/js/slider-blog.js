require(['jquery', 'slick'], function($) {
    $('.blog-slider-wrapper').slick({
        dots: true,
        infinite: false,
        speed: 300,
        slidesToShow: 1,
        adaptiveHeight: false
    });
});
