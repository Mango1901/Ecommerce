define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/sidebar',
    'Magento_Ui/js/model/messageList',
    'mage/translate'
], function ($, ko, Component, stepNavigator, sidebarModel, messageList, $t) {
    'use strict';

    return Component.extend({
        template: 'Magento_Checkout/summary/navigator-btn',

        initialize: function () {
            var clientY = false;
            $('body').on('touchstart', '.touch-bar', function (e) {
                clientY = e.originalEvent.touches[0].pageY;
            }).on('touchend', '.touch-bar', function (e) {
                if (e.originalEvent.changedTouches[0].pageY - clientY > 50) {
                    sidebarModel.hide();
                }
            });
            return this._super();
        },

        /** @inheritdoc */
        continueButton: function () {
            sidebarModel.hide();
            $('#co-shipping-method-form').submit();
            return false;
        },

        placeOrderButton: function () {
            var placeOrderBtn = $('._active button.action.primary.checkout');
            if (placeOrderBtn.length) {
                placeOrderBtn.click();
            } else {
                messageList.addErrorMessage({
                    message: $t('Please select a payment method.')
                });
            }
        },

        backToPrevious: function () {
            if (window.location.hash === "#shipping") {
                history.back();
            }
            history.back();
            return false;
        },

        isDisplayContinue: ko.computed(function () {
            if (Array.isArray(stepNavigator.steps()) && stepNavigator.steps().length === 2) {
                return stepNavigator.getActiveItemIndex() === 0;
            }
        }),

        isDisplayPlaceOrder: ko.computed(function () {
            if (Array.isArray(stepNavigator.steps())) {
                return stepNavigator.getActiveItemIndex() === 0;
            }
        })
    });
});
