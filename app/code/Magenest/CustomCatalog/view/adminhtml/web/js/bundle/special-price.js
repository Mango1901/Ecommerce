/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Ui/js/form/element/abstract',
    'uiRegistry',
    'underscore'
], function (Abstract, registry, _) {
    'use strict';

    return Abstract.extend({
        defaults: {
            price: 'product_form.product_form.product-details.container_price.price',
            updateLink: '',
            exports: {
                calculatedVal: '${ $.parentName }.special_price:value'
            }
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();

            this.price = registry.get(this.price);
            this.initPriceListener();
        },

        /**
         * @inheritdoc
         */
        onUpdate: function () {
            this._super();
            this.calculate();
        },

        /**
         * Handles event on price input.
         */
        initPriceListener: function () {
            this.price.on('value', this.calculate.bind(this));
        },

        /**
         * Calculate
         */
        calculate: function () {
            var price = this.price.value(),
                value = this.value();

            if (value == '') {
                this.set('calculatedVal', '');
                return;
            }

            price = this.parseNumber(price);
            value = this.parseNumber(value);

            if (_.isNaN(price) || _.isNaN(value)) {
                return;
            }

            value = ((Math.min(price, value) / price) * 100);
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
