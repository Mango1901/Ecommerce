define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'mage/storage',
        'mage/translate',
        'Magento_Checkout/js/model/resource-url-manager',
        'Magenest_RewardPoints/js/action/get-payment-information',
        'Magento_Checkout/js/model/totals',
        'Magenest_RewardPoints/js/model/payment/messages',
        'mage/url',
        'uiRegistry'
    ],
    function ($, ko, Component, quote, storage, $t, urlManager, getPaymentInformationAction, totals, messageContainer, url, registry) {
        'use strict';
        return Component.extend(
            {
                defaults: {
                    template: 'Magenest_RewardPoints/payment/rewardpoints'
                },
                isRewardTiersEnable: window.checkoutConfig.getRewardTiersEnable,
                isRewardPointEnable: window.checkoutConfig.isRewardPointEnable,
                earnPointWithAppliedPoints: window.checkoutConfig.earnPointWithAppliedPoints,
                earnPointWithAppliedDiscount: window.checkoutConfig.earnPointWithAppliedDiscount,
                hasRewardPoint: false,
                rewardPointAmount: '',
                maxAppliedPoint: 0,
                currentPoint: '',
                configPoint: '',
                currency: '',
                point_total: registry.get("index=point-total"),
                delayTime: 1000,
                feedUrl: 'rewardpoints/quote/load',
                cancelUrl: 'rewardpoints/quote/remove',
                submitUrl: 'rewardpoints/quote/add',
                baseUrl: url.build(''),
                coupont: ko.observable(0),
                initialize: function () {
                    this._super();
                    window.appliedPointFunc = this.appliedPointTimeOutTick.bind(this);
                    window.recalculateEarnPoint = this.recalculateEarnPoint.bind(this);
                    return this;
                },

                getInputTarget: function () {
                    return $('#rewardpoints-quantity');
                },
                checkKey: function (event) {
                    if (event.keyCode == 13 || event.keyCode == 10) {
                        this.getInputTarget().change();
                        this.apply();
                    }
                    return event.keyCode != 13 && event.keyCode != 10;
                },
                initObservable: function () {
                    this._super()
                        .observe(
                            {
                                hasRewardPoint: false,
                                rewardPointAmount: '',
                                maxAppliedPoint: 0,
                                currency: window.checkoutConfig.currency,
                                currentPoint: window.checkoutConfig.currentPoint,
                                configPoint: window.checkoutConfig.configPoint,
                                currencyPoint:  window.checkoutConfig.currentPoint / window.checkoutConfig.configPoint,
                            },
                        );
                    this.recalculateEarnPoint(true);
                    return this;
                },

                appliedPointTimeOutTick: function () {
                    var self = this;
                    storage.post(
                        url.build('rest/V1/magenest/rewardpoint/calculateMaxAppliedPoint'),
                    ).always(
                        function (response) {
                            self.maxAppliedPoint(response);
                        }
                    );
                },

                recalculateEarnPoint: function (init = false) {
                    var self = this;
                    totals.isLoading(true);
                    storage.post(
                        url.build('rest/V1/magenest/rewardpoint/loadPoint'),
                    ).always(
                        function (response) {
                            var data = JSON.parse(response);
                            if (data.pointEarned !== undefined && parseFloat(data.pointEarned) > 0.00) {
                                var point = data.pointEarned;
                                self.point_total.pointEarn(point);
                            } else {
                                self.point_total.pointEarn(0);
                            }
                            if (init)
                                if (data.rewardPointAmount !== undefined && parseInt(data.rewardPointAmount) !== 0) {
                                    self.rewardPointAmount(data.rewardPointAmount);

                                    if (parseInt(data.rewardPointAmount) > 0) {
                                        self.hasRewardPoint(true);
                                    }
                                }
                            totals.isLoading(false);
                        }
                    );
                },

                apply: function () {
                    var self = this;

                    if ($.isNumeric(self.getInputTarget().val())) {
                        var value = parseFloat(self.getInputTarget().val());
                    } else {
                        value = -1;
                    }
                    var currentPoint = parseFloat(self.currentPoint());
                    if (value > self.maxAppliedPoint()) {
                        messageContainer.addErrorMessage({
                            'message': $t('Please apply a valid point amount.')
                        });
                        self.rewardPointAmount('');
                        return;
                    }
                    if ((value <= 0) || (value > currentPoint) || Math.ceil(value) != value) {
                        self.hasRewardPoint(false);
                        messageContainer.addErrorMessage({
                            'message': $t('Please apply a valid point amount.')
                        });
                        self.rewardPointAmount('');
                        return;
                    }
                    if (value > 0 && (value <= parseFloat(self.currentPoint()))) {
                        storage.post(
                            url.build('rest/V1/magenest/rewardpoint/addPoint'),
                            JSON.stringify({
                                point: value
                            })
                        ).done(
                            function (response) {
                                if (response) {
                                    var deferred = $.Deferred();
                                    self.hasRewardPoint(true);
                                    totals.isLoading(true);
                                    getPaymentInformationAction(deferred);
                                    if (!self.earnPointWithAppliedPoints) {
                                        self.point_total.pointEarn(0);
                                    }
                                    $.when(deferred).done(function () {
                                        self.recalculateEarnPoint();
                                    });
                                    messageContainer.addSuccessMessage({
                                        'message': $t('Points applied successfully.')
                                    });
                                }
                            }
                        ).fail(
                            function () {
                                messageContainer.addErrorMessage({
                                    'message': $t('Points applied unsuccessfully. ')
                                });
                            }
                        );
                    }
                },

                applyAll: function () {
                    var self = this;
                    var currentPoint = parseFloat(self.currentPoint());
                    var maxAppliedPoint = parseFloat(self.maxAppliedPoint());
                    var appliedPoint = maxAppliedPoint > currentPoint ? currentPoint : maxAppliedPoint;
                    self.getInputTarget().val(appliedPoint);
                    self.getInputTarget().change();
                    self.apply();
                },

                cancel: function () {
                    var self = this;
                    storage.post(
                        url.build('rest/V1/magenest/rewardpoint/cancelPoint'),
                    ).done(
                        function (response) {
                            if (response) {
                                var deferred = $.Deferred();
                                totals.isLoading(true);
                                getPaymentInformationAction(deferred);
                                $.when(deferred).done(function () {
                                    self.hasRewardPoint(false);
                                    self.rewardPointAmount('');
                                    self.recalculateEarnPoint();
                                });
                                messageContainer.addSuccessMessage({
                                    'message': $t('Points deleted successfully.')
                                });
                            }
                        }
                    ).fail(
                        function () {
                            messageContainer.addErrorMessage({
                                'message': $t('Points deleted unsuccessfully. ')
                            });
                        }
                    );
                },
            }
        );
    }
);
