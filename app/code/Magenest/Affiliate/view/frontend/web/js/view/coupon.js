
define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magenest_Affiliate/js/action/apply-coupon',
    'Magenest_Affiliate/js/action/cancel-coupon',
    'Magenest_Affiliate/js/model/coupon'
], function ($, ko, Component, quote, setCouponCodeAction, cancelCouponAction, affiliateCoupon) {
    'use strict';

    var totals              = quote.getTotals(),
        affiliateCouponCode = affiliateCoupon.getAffiliateCouponCode(),
        isApplied           = affiliateCoupon.getIsApplied(),
        affiliateSource     = $.mage.cookies.get('affiliate_source'),
        affiliateKey        = $.mage.cookies.get('affiliate_key');

    if (totals() && affiliateSource === 'coupon' && affiliateKey) {
        affiliateCouponCode(affiliateKey);
    }

    isApplied(affiliateCouponCode() != null);

    return Component.extend({
        defaults: {
            template: 'Magenest_Affiliate/checkout/cart'
        },
        affiliateCouponCode: affiliateCouponCode,
        isLoading: affiliateCoupon.isLoading,

        /**
         * Applied flag
         */
        isApplied: isApplied,

        /**
         * Coupon code application procedure
         */
        apply: function () {
            if (this.validate()) {
                setCouponCodeAction(affiliateCouponCode(), isApplied);
            }
        },

        /**
         * Cancel using coupon
         */
        cancel: function () {
            if (this.validate()) {
                affiliateCouponCode('');
                cancelCouponAction(isApplied);
            }
        },

        /**
         * Coupon form validation
         *
         * @returns {Boolean}
         */
        validate: function () {
            var form = '#affiliate-coupon-discount-form';

            return $(form).validation() && $(form).validation('isValid');
        }
    });
});
