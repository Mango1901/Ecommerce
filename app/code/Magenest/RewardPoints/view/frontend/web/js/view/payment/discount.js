define([
    'uiRegistry',
    'mage/url',
    'jquery',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/action/get-payment-information',
], function (registry, url, $, totals, getPaymentInformationAction) {
    'use strict';

    var mixin = {
        apply: function () {
            this._super();

            if (this.validate()) {
                if (window.checkoutConfig.earnPointWithAppliedDiscount === false) {
                    registry.get("index=point-total").pointEarn(0);
                } else {
                    var deferred = $.Deferred();
                    totals.isLoading(true);
                    getPaymentInformationAction(deferred);
                    $.when(deferred).done(function () {
                        $.ajax(
                            {
                                url: url.build('') + 'rewardpoints/quote/load'
                            }
                        ).always(
                            function (response) {
                                var point = 0;
                                if (response.pointEarned !== undefined && parseFloat(response.pointEarned) > 0.00) {
                                    point = response.pointEarned;
                                }
                                registry.get("index=point-total").pointEarn(point);
                            }
                        );
                        totals.isLoading(false);
                    });
                }
            }
        },

        cancel: function () {
            if (this.validate()) {
                this._super();

                var deferred = $.Deferred();
                totals.isLoading(true);
                getPaymentInformationAction(deferred);
                $.when(deferred).done(function () {
                    $.ajax(
                        {
                            url: url.build('') + 'rewardpoints/quote/load'
                        }
                    ).always(
                        function (response) {
                            var point = 0;
                            if (response.pointEarned !== undefined && parseFloat(response.pointEarned) > 0.00) {
                                point = response.pointEarned;
                            }
                            registry.get("index=point-total").pointEarn(point);
                        }
                    );
                    totals.isLoading(false);
                });
            }
        }
    };

    return function (Component) {
        return Component.extend(mixin);
    };

});