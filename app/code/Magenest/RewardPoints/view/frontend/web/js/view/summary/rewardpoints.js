define(
    [
        'jquery',
        'ko',
        'Magenest_RewardPoints/js/view/payment/rewardpoints',
        'Magenest_RewardPoints/js/action/get-payment-information',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'mage/url',
        'uiRegistry',
        'Magento_Catalog/js/price-utils'
    ],
    function ($, ko, rewardpoints, getPaymentInformationAction, Component, quote, totals, urlBuilder, registry) {
        'use strict';
        return Component.extend(
            {
                defaults: {
                    template: 'Magenest_RewardPoints/summary/rewardpoints'
                },
                point_total: registry.get("index=point-total-cart"),
                isIncludedInSubtotal: window.checkoutConfig.isIncludedInSubtotal,
                totals: totals.totals,
                getRewardPointsSegment: function () {
                    var reward = totals.getSegment('reward-magenest');
                    if (reward !== null && reward.hasOwnProperty('value')) {
                        return reward.value;
                    }
                    return 0;
                },
                getTitle: function () {
                    var rewardTitle = totals.getSegment('reward-magenest').title;
                    if (rewardTitle !== null) return rewardTitle;
                    return 'Reward Discount';
                },
                getValue: function () {
                    var self = this;
                    var grandTotals = totals.getSegment('grand_total');
                    if (grandTotals.value < 0) {
                        $.ajax({
                                url: urlBuilder.build('rewardpoints/quote/process/'),
                                type: 'post',
                                data: {'grand_total': grandTotals.value, isCart : self.isCartPage() ? 1 : 0},
                                showLoader: true
                            }
                        ).done(function () {
                            var deferred = $.Deferred();
                            getPaymentInformationAction(deferred);
                            totals.isLoading(true);
                            $.when(deferred).done(function () {
                                if (self.isCartPage()) {
                                    self.recalculateEarnPoint();
                                } else {
                                    window.recalculateEarnPoint(true);
                                }
                            });
                        });
                    }
                    return this.getFormattedPrice(this.getRewardPointsSegment());
                },
                isDisplayed: function () {
                    if (!this.isCartPage()) {
                        window.appliedPointFunc();
                    }
                    return this.isFullMode() && this.getRewardPointsSegment() < 0;
                },

                isCartPage: function () {
                    var url = window.location.href;
                    var baseUrl = urlBuilder.build('');
                    if (baseUrl) {
                        var baseurlLength = baseUrl.length;
                        var compareUrl = url.substr(baseurlLength, url.length);
                        return !(compareUrl.substr(0, 13) !== 'checkout/cart');
                    }
                    return false;
                },

                recalculateEarnPoint: function () {
                    var self = this;
                    var quoteFeedUrl = urlBuilder.build('rewardpoints/quote/load');
                    totals.isLoading(true);
                    $.ajax(
                        {
                            url: quoteFeedUrl
                        }
                    ).always(
                        function (response) {
                            if (response.pointEarned !== undefined && parseFloat(response.pointEarned) > 0.00) {
                                var point = response.pointEarned;
                                self.point_total.pointEarn(point);
                            } else {
                                self.point_total.pointEarn(0);
                            }
                            totals.isLoading(false);
                        }
                    );
                },
            }
        );
    }
);