
define([
    'ko',
    'domReady!'
], function (ko) {
    'use strict';

    var affiliateCouponCode = ko.observable(null),
        isApplied           = ko.observable(null);

    return {
        affiliateCouponCode: affiliateCouponCode,
        isApplied: isApplied,
        isLoading: ko.observable(false),

        /**
         * @return {*}
         */
        getAffiliateCouponCode: function () {
            return affiliateCouponCode;
        },

        /**
         * @return {Boolean}
         */
        getIsApplied: function () {
            return isApplied;
        }
    };
});
