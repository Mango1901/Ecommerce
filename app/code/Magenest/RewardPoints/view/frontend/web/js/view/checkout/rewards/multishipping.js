define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Customer/js/customer-data',
    'mage/url',
], function($, modal, customerData, url) {
    "use strict";

    $('#apply').click(function () {
        if ($.isNumeric($('#rewardpoints-quantity').val())) {
            var value = parseFloat($('#rewardpoints-quantity').val());
        } else {
            value = -1;
        }
        var pointMax = parseInt($("#pointMax").attr("value"));
        var currentPoint = parseFloat($(this).data('current'));
        if (value <=0 || (value > currentPoint) || Math.ceil(value) != value || value > pointMax) {
            showError();
            return false;
        }
        applyPoint(value,currentPoint);
    });

    $('#apply-all').click(function () {
        var value = $(this).data('pointMax');
        var currentPoint = $(this).data('current');
        applyPoint(value,currentPoint);
    });

    //Apply Point
    function applyPoint(value,currentPoint) {
        if (value > 0 && (value <= parseFloat(currentPoint))) {
            var submitUrl = 'rewardpoints/quote/add';
            var baseUrl = url.build('');
            $.ajax({
                    url: baseUrl + submitUrl,
                    type: 'post',
                    data: {
                        rewardpoints_quantity: value
                    },
                    showLoader: true
                }
            ).done(
                function () {
                    var msg = $.mage.__('Points applied successfully.');
                    customerData.set('messages', {
                        messages: [{
                            type: 'success',
                            text: msg
                        }]
                    });
                    window.location.reload();
                },
            ).fail(
                function () {
                    showError();
                }
            );
        }
    }

    $(document).ready(function(){
        var point = parseInt($("#point").attr("value"));
        var pointMax = parseInt($("#pointMax").attr("value"));
        if (point > pointMax) {
            cancel();
        }
        if (point) {
            $("#rewardpoints-quantity").prop('disabled',true);
            $('#cancel').show();
            $('#apply-all').hide();
            $('#apply').hide();
        } else {
            $('#cancel').hide();
        }
    });


    $('#cancel').click(function () {
        cancel();
    });

    function showError() {
        var msg = $.mage.__('Please apply a valid point amount.');
        customerData.set('messages', {
            messages: [{
                type: 'error',
                text: msg
            }]
        });
    }

    //Cancel Point
    function cancel() {
        var cancelUrl = 'rewardpoints/quote/remove';
        var baseUrl = url.build('');
        $.ajax({
            url: baseUrl + cancelUrl,
            type: 'DELETE',
            contentType: 'json',
            showLoader: true
        }).done(
            function () {
                var msg = $.mage.__('Points deleted successfully.');
                customerData.set('messages', {
                    messages: [{
                        type: 'success',
                        text: msg
                    }]
                });
                window.location.reload();
            }
        ).fail(
            function () {
                showError();
            }
        );
    }
});
