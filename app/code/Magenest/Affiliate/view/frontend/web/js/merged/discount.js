/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magenest_Affiliate/js/merged/apply-coupon',
    'Magenest_Affiliate/js/merged/cancel-coupon',
    'Magento_SalesRule/js/model/coupon',
    'Magenest_Affiliate/js/model/coupon'
], function ($, ko, Component, quote, setAffiliateCoupon, cancelAffiliateCoupon, coupon, affiliateCoupon) {
    'use strict';

    var totals = quote.getTotals(),
        couponCode = coupon.getCouponCode(),
        affiliateCouponCode = affiliateCoupon.getAffiliateCouponCode(),
        isApplied = coupon.getIsApplied(),
        isAffiliateApplied = affiliateCoupon.getIsApplied(),
        affiliateSource = $.mage.cookies.get('affiliate_source'),
        affiliateKey = $.mage.cookies.get('affiliate_key');

    if (totals()) {
        if (affiliateSource === 'coupon' && affiliateKey) {
            couponCode(affiliateKey);
        } else {
            couponCode(totals()['coupon_code']);
        }
    }

    isApplied(couponCode() != null);
    isAffiliateApplied(affiliateCouponCode() != null)

    return Component.extend({
        defaults: {
            template: 'Magento_SalesRule/payment/discount'
        },
        couponCode: couponCode,
        affiliateCouponCode: affiliateCouponCode,

        /**
         * Applied flag
         */
        isApplied: isApplied,
        isAffiliateApplied: isAffiliateApplied,

        /**
         * Coupon code application procedure
         */
        apply: function () {
            if (this.validate()) {
                setAffiliateCoupon(couponCode(), isApplied);
            }
        },

        /**
         * Cancel using coupon
         */
        cancel: function () {
            if (this.validate()) {
                couponCode('');
                affiliateCouponCode('');
                cancelAffiliateCoupon(isApplied);
            }
        },

        /**
         * Coupon form validation
         *
         * @returns {Boolean}
         */
        validate: function () {
            var form = '#discount-form';

            return $(form).validation() && $(form).validation('isValid');
        }
    });
});
