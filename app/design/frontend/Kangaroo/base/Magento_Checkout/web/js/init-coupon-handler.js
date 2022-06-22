require([
    'jquery',
    'mage/storage',
    'Magento_Checkout/js/model/quote',
    'Magenest_Affiliate/js/model/resource-url-manager',
    'loader'
], function ($, storage, quote, urlManager) {
    let stop = true, quoteId = quote.getQuoteId();
    $('#discount-coupon-form').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    }).submit(function (e) {
        if (stop) {
            e.preventDefault();
        }
    });

    $('#apply_coupon').click(function () {
        let form = $('#discount-coupon-form');
        if (form.validation() && form.validation('isValid')) {
            let url = urlManager.getApplyAffiliateCouponUrl($("#coupon_code").val(), quoteId);
            $('body').loader("show");
            storage.put(url, {}, false, null, {}).done(function (response) {
                if (response) {
                    window.location.reload();
                }
            }).fail(function () {
                stop = false;
                $('#discount-coupon-form').submit();
            });
        }
    });

    $('#cancel_coupon').click(function () {
        $('body').loader("show");

        let url = urlManager.getCancelAffiliateCouponUrl(quoteId);
        storage.delete(url, false).done(function () {
            stop = false;
            $('#discount-coupon-form').submit();
        });
    });
});
