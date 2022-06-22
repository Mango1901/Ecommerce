define([
        'jquery',
        'underscore',
        'Magento_Ui/js/modal/modal',
        'mage/url',
        'mage/translate',
        'jquery/jquery.cookie'
    ],
    function($, _, modal, urlBuilder, $t) {
        'use strict';

        var options = {
            type: 'popup',
            responsive: true,
            modalClass: 'location-popup',
            buttons: [],
        };

        var popup = modal(options, $('.header-store-menu'));

        $('.header-store-action').on('click', function () {
            $('.header-store-menu').modal('openModal');
        });

        _.debounce(function () {
            if(!$.cookie('area_code') && !$.cookie('notify_area_popup')) {
                $('.header-store-menu').modal('openModal');
                $.cookie('notify_area_popup', true);
            }
        }, 2000)();


        $('#area-popup-modal').on('submit',function (event) {
            event.preventDefault();
            const areaCode = $('.popup-form-data-submit').serializeArray()[0]['value'];
            const areaLabel = $("label[for='"+areaCode+"']").text();
            assignAreaCodeToQuote(areaCode, areaLabel);
            $(".header-store-menu").modal("close");
        });

        /**
         * Function used to assign area code to quote
         */
         function assignAreaCodeToQuote(areaCode, areaLabel) {
            let url = urlBuilder.build("customsource/quote/assignareacodetoquote");
            $("#area-prefix").text($t('Area'));
            $("#area-label").text(': ' + areaLabel);
            // use ajax function to save the data
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    area_code: areaCode,
                },
                showLoader: true,
                complete: function(response) {
                    if (response.responseJSON.success) {
                        window.location.reload(true);
                    }
                },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                }
            });
        }
    }
);
