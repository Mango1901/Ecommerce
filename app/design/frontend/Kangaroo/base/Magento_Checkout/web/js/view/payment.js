/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'uiComponent',
    'uiRegistry',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'mage/translate'
], function (
    $,
    _,
    Component,
    registry,
    ko,
    quote,
    stepNavigator,
    paymentService,
    methodConverter,
    getPaymentInformation,
    checkoutDataResolver,
    $t
) {
    'use strict';

    /** Set payment methods to collection */
    paymentService.setPaymentMethods(methodConverter(window.checkoutConfig.paymentMethods));

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/payment',
            activeMethod: ''
        },
        isVisible: ko.observable(quote.isVirtual()),
        quoteIsVirtual: quote.isVirtual(),
        isPaymentMethodsAvailable: ko.computed(function () {
            return paymentService.getAvailablePaymentMethods().length > 0;
        }),
        saveCompanyVat: ko.observable(false),
        companyName: ko.observable(false),
        companyEmail: ko.observable(false),
        companyAddress: ko.observable(false),
        taxCode: ko.observable(false),

        /** @inheritdoc */
        initialize: function () {
            this._super();
            checkoutDataResolver.resolvePaymentMethod();
            stepNavigator.registerStep(
                'payment',
                null,
                $t('Review & Payments'),
                this.isVisible,
                _.bind(this.navigate, this),
                this.sortOrder
            );
            window.addEventListener('hashchange', _.bind(function () {
                var hashString = window.location.hash.replace('#', '');
                this.isVisible(hashString === "payment");
                if (hashString !== "payment") {
                    $('.checkout-payment-method').hide();
                } else {
                    $('.checkout-payment-method').show();
                }
            }, this));

            var self = this;

            registry.async('checkout.steps.shipping-step.shippingAddress.after-form.additional-data.vat_information_wrapper.save_vat_invoice')(function (saveCompanyVat) {
                self.saveCompanyVat = saveCompanyVat.value;
            });
            registry.async('checkout.steps.shipping-step.shippingAddress.after-form.additional-data.vat_information_wrapper.company_name')(function (companyName) {
                self.companyName = companyName.value;
            });
            registry.async('checkout.steps.shipping-step.shippingAddress.after-form.additional-data.vat_information_wrapper.company_email')(function (companyEmail) {
                self.companyEmail = companyEmail.value;
            });
            registry.async('checkout.steps.shipping-step.shippingAddress.after-form.additional-data.vat_information_wrapper.tax_code')(function (taxCode) {
                self.taxCode = taxCode.value;
            });
            registry.async('checkout.steps.shipping-step.shippingAddress.after-form.additional-data.vat_information_wrapper.company_address')(function (companyAddress) {
                self.companyAddress = companyAddress.value;
            });

            return this;
        },

        /**
         * Navigate method.
         */
        navigate: function () {
            var self = this;

            if (!self.hasShippingMethod()) {
                this.isVisible(false);
                stepNavigator.setHash('shipping');
            } else {
                getPaymentInformation().done(function () {
                    self.isVisible(true);
                });
            }
        },

        /**
         * @return {Boolean}
         */
        hasShippingMethod: function () {
            return window.checkoutConfig.selectedShippingMethod !== null;
        },

        /**
         * @return {*}
         */
        getFormKey: function () {
            return window.checkoutConfig.formKey;
        }
    });
});
