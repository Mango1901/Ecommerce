define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'jquery',
        'ko',
        'mage/translate'
    ],
    function (Component, quote, $, ko) {
        return Component.extend({
            defaults: {
                pointEarn: 0,
                pointsLabel: '',
                includeTierReward: '',
                isDisplayedPoints: false
            },
            initObservable: function () {
                var self = this;
                self._super().observe(['pointsLabel', 'pointEarn', 'isDisplayedPoints', 'includeTierReward']);
                self.pointEarn(self.getValue());
                self.pointsLabel(self.getPointUnit());
                if (window.checkoutConfig.isIncludeTierReward) {
                    self.includeTierReward($.mage.__('(Included ' + window.checkoutConfig.tierReward + self.getPointUnit() + ' for Tier Reward)'))
                } else {
                    self.includeTierReward('');
                }

                this.isDisplayedPoints = ko.pureComputed(function () {
                    return !!self.pointEarn() && parseFloat(self.pointEarn()) > 0.00;
                });

                return this;
            },

            totals: quote.getTotals(),

            getValue: function () {
                return window.checkoutConfig.checkoutRewardPointsEarn;
            },

            getPointUnit: function () {
                return window.checkoutConfig.checkoutRewardPointsLabel;
            }
        });
    }
);
