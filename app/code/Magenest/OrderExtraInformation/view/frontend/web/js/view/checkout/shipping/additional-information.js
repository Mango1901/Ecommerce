/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([ // jshint ignore:line
    'jquery',
    "underscore",
    'Magento_Ui/js/form/form',
    'ko',
    'uiRegistry',
    'mage/translate',
    'Magento_Checkout/js/model/quote',
    'mage/template'
], function ($, _, Component, ko, registry, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            formElement: '#shipping-additional-information'
        },

        /**
         * Initialize
         *
         * @returns {exports.initialize}
         */
        initialize: function () {
            var self = this, originAction;
            this._super();

            registry.async('checkout.steps.shipping-step.shippingAddress')(function (shipping) {
                originAction = shipping.setShippingInformation.bind(shipping);
                shipping.setShippingInformation = function () {
                    if (self.validate()) {
                        originAction();
                    }
                }
            });

            return this;
        },

        /**
         * Validate
         *
         * @returns {boolean}
         */
        validate: function () {
            this.source.set('params.invalid', false);
            this.source.trigger('data.validate');

            return !Boolean(this.source.get('params.invalid'));
        }
    });
});