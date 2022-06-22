define([
    'jquery',
    'mage/url',
    'mage/storage',
],function ($, urlBuilder, storage, ) {
    'use strict';
    return function (config) {
        function filterReview(paging = null, value = null){
            var url = config.sortUrl,
                params = {
                    value: value,
                    p: paging
                },
                fromPages = false;
            $.ajax({
                url: url,
                cache: true,
                dataType: 'html',
                showLoader: false,
                loaderContext: $('.product.data.items'),
                data: params,
                beforeSend: function () {
                    $('#customer-reviews').trigger('processStart');
                },
                success: function () {
                    $('#customer-reviews').trigger('processStop');
                },
            }).done(function (data) {
                $('#product-review-container').html("");
                $('#product-review-container').html(data).trigger('contentUpdated');
                $('[data-role="product-review"] .pages a').each(function (index, element) {
                    $(element).click(function (event) {
                        fromPages = true;
                        event.preventDefault();
                    });
                });
            }).complete(function () {

                if (fromPages == true) {
                    $('html, body').animate({
                        scrollTop: $('#reviews').offset().top - 50
                    }, 300);
                }
            });
        }
        $("#product-review-container").delegate('.magenest-paging', 'click', function () {
            var paging = $(this).find(".magenest-paging-item").text();
            filterReview(paging);
        });
        $("#product-review-container").delegate('.pages-item-previous', 'click', function () {
            var paging = $(this).find(".magenest-paging-item").text();
            filterReview(paging);
        });
        $("#product-review-container").delegate('.pages-item-next', 'click', function () {
            var paging = $(this).find(".magenest-paging-item").text();
            filterReview(paging);
        });
        $('.magenest-photoreview-action').on('click', function () {
            filterReview();
        });

        $('.magenest-photoreview-filter button').on('click', function () {
            const valueFilter = $(this).val();
            filterReview(null, valueFilter);
            $('.filter-option.active').removeClass('active')
            $(this).addClass('active');
        });
    }
});
