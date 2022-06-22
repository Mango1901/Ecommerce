/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Catalog/js/tier-price/percentage-processor',
    'underscore',
    'Magento_Ui/js/lib/view/utils/async',
    'uiRegistry',
    'rjsResolver'
], function (Element, _, $, registry, resolver) {
    'use strict';

    return Element.extend({
        defaults: {
            priceElem: '${ $.parentName }.price',
            percentElem: '${ $.parentName }.percentage_value',
            price: 'product_form.product_form.product-details.container_price.price',
            selector: 'input',
            imports: {
                priceValue: '${ $.priceElem }:priceValue'
            },
            exports: {
                calculatedVal: '${ $.percentElem }:value'
            },
            input: null
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            var self = this;
            this._super();

            resolver(function () {
                self.price = registry.get(self.price);
            }.bind(this));

            return this;
        },

        /**
         * @inheritdoc
         */
        onInput: function (event) {
            var value = event.currentTarget.value,
                price = this.price.value();

            if (value == '') {
                this.set('calculatedVal', '');
                return;
            }

            price = this.parseNumber(price);
            value = this.parseNumber(value);

            if (_.isNaN(price) || _.isNaN(value)) {
                return;
            }

            value = (100 - (Math.min(price, value) / price) * 100);
            this.set('calculatedVal', value);
        },

        /**
         * Parse price string.
         *
         * @param {String} value
         * @return {Number}
         */
        parseNumber: function (value) {
            var isDot, isComa;

            if (typeof value !== 'string') {
                return parseFloat(value);
            }

            isDot = value.indexOf('.');
            isComa = value.indexOf(',');

            if (isDot !== -1 && isComa !== -1) {
                if (isComa > isDot) {
                    value = value.replace(/\./g, '').replace(',', '.');
                } else {
                    value = value.replace(/,/g, '');
                }
            } else if (isComa !== -1) {
                value = value.replace(/,/g, '.');
            }

            return parseFloat(value);
        }
    });
});
