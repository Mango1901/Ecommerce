define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magenest_Affiliate/js/model/resource-url-manager',
    'Magento_Checkout/js/model/error-processor',
    'Magenest_Affiliate/js/model/messageList',
    'mage/storage',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals',
    'mage/translate',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/recollect-shipping-rates',
    'Magento_Checkout/js/action/get-totals',
    'Magenest_Affiliate/js/model/coupon',
    'Magento_SalesRule/js/action/cancel-coupon'
], function (
    $,
    quote,
    urlManager,
    errorProcessor,
    messageContainer,
    storage,
    getPaymentInformationAction,
    totals,
    $t,
    fullScreenLoader,
    recollectShippingRates,
    getTotalsAction,
    couponModel,
    cancelDefault
) {
    'use strict';

    /**
     * Cancel applied coupon.
     *
     * @param {Boolean} isApplied
     * @returns {Deferred}
     */
    return function (isApplied) {
        var quoteId = quote.getQuoteId(),
            url = urlManager.getCancelAffiliateCouponUrl(quoteId),
            message = $t('Your affiliate coupon was successfully removed.');

        messageContainer.clear();
        fullScreenLoader.startLoader();

        return storage.delete(
            url,
            false
        ).done(function () {
            var deferred = $.Deferred();

            totals.isLoading(true);
            couponModel.isLoading(true);
            recollectShippingRates();
            if ($('body').hasClass('checkout-cart-index')) {
                getTotalsAction([], deferred);
            } else {
                getPaymentInformationAction(deferred);
            }
            $.when(deferred).done(function () {
                isApplied(false);
                totals.isLoading(false);
                couponModel.isLoading(false);
                fullScreenLoader.stopLoader();
                cancelDefault(isApplied).success(function (result) {
                    if (result) {
                        $('#submit_coupon').attr('disabled', false);
                    }
                });
            });
            messageContainer.addSuccessMessage({
                'message': message
            });
        }).fail(() => {
            fullScreenLoader.stopLoader();
            cancelDefault(isApplied).success(function (result) {
                if (result) {
                    $('#submit_coupon').attr('disabled', false);
                }
            });
        });
    };
});
