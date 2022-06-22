

define(
    [
        'jquery',
        'Magento_Checkout/js/model/resource-url-manager',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, resourceUrlManager, quote) {
        "use strict";

        return $.extend(resourceUrlManager, {
            getApplyAffiliateCouponUrl: function (code) {
                var params = this.getCheckoutMethod() === 'guest' ? {quoteId: quote.getQuoteId()} : {},
                    urls   = {
                        'guest': '/guest-carts/:quoteId/mpaffiliatecoupons/' + code,
                        'customer': '/carts/mine/mpaffiliatecoupons/' + code
                    };

                return this.getUrl(urls, params);
            },

            /**
             * @param {String} quoteId
             * @return {*}
             */
            getCancelAffiliateCouponUrl: function (quoteId) {
                var params = this.getCheckoutMethod() === 'guest' ? {quoteId: quote.getQuoteId()} : {},
                    urls = {
                        'guest': '/guest-carts/' + quoteId + '/mpaffiliatecoupons/',
                        'customer': '/carts/mine/mpaffiliatecoupons/'
                    };

                return this.getUrl(urls, params);
            },
        });
    }
);
