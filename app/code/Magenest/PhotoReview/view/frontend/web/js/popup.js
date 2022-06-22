define([
    'jquery',
    'mage/storage',
    'underscore',
    'mage/url',
    'Magento_Ui/js/modal/modal'
], function ($,storage, _, urlBuilder, modal) {
    'use strict';

    var options = {
        type: 'popup',
        responsive: true,
        modalClass: 'popup-review-image',
        buttons: [{
            class: '',
            click: function () {
                this.closeModal();
            }
        }]
    };

    var popup = modal(options, $('#magenest-photoreview-popup'));

    $("#product-review-container").delegate('.photo-item', 'click', function () {
        var photoId = $(this).data('photoId'),
            reviewId = $(this).data('reviewId'),
            url = urlBuilder.build('photoreview/review/index'),
            data = {
                photoId: photoId,
                reviewId: reviewId
            };
        popup.openModal();
        getPopup(url,data);
    });
    $(".photoreview-image").on('click', function () {
        var photoId = $(this).data('photoId'),
            reviewId = $(this).data('reviewId'),
            url = urlBuilder.build('photoreview/review/index'),
            data = {
                photoId: photoId,
                reviewId: reviewId
            };
        getPopup(url,data);
    });
    function getPopup(url,data) {
        $.ajax({
            url: url,
            cache: true,
            showLoader: false,
            data: data,
            type: 'POST',
        }).done(function (response) {
            if(response != ''){
                $("#magenest-photoreview-popup").html(response);
                $("#magenest-photoreview-popup").addClass("opened");

                $(".review-content").addClass("mobile");
                $(".review-popup-overplay").on('click', function () {
                    $("#magenest-photoreview-popup").html("");
                    $("#magenest-photoreview-popup").removeClass("opened");

                    $(".review-content").removeClass("mobile");
                });
                $('.review-popup-close').on('click', function () {
                    $("#magenest-photoreview-popup").html("");
                    $("#magenest-photoreview-popup").removeClass("opened");

                    $(".review-content").removeClass("mobile");
                });
                jQuery(document).keyup(function(e) {
                    if (e.which == 27) {
                        $("#magenest-photoreview-popup").html("");
                        $("#magenest-photoreview-popup").removeClass("opened");

                        $(".review-content").removeClass("mobile");
                    }
                });
                $('.action-show-all').on('click', function () {
                    $(".review-content").removeClass("mobile");
                    $(".popup-pros-cons").show();
                    $(".review-container").addClass("top");
                    $(".close-content").show();
                    $(this).hide();
                });
                $('.action-close').on('click', function () {
                    $(".review-content").addClass("mobile");
                    $(".popup-pros-cons").hide();
                    $(".review-container").removeClass("top");
                    $(".close-content").hide();
                    $(".action-show-all").show();
                    $(this).parent("close-content").hide();
                });
            }
        });
    }
});
