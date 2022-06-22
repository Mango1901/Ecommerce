require([
    'jquery'
], function ($) {
    'use strict';

    $(".photoreview-image").click(function () {
        $(this).siblings(".review-popup").addClass("opened");
        $("body").addClass("_has-modal");
    });
    $(".review-popup-overplay").click(function(){
        $(".review-popup").removeClass("opened");
        $("body").removeClass("_has-modal");
    });

});