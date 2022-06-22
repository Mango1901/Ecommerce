/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'mage/template',
    'priceUtils',
    'priceBox',
    'jquery-ui-modules/widget'
], function ($, _, mageTemplate, utils) {
    'use strict';

    var globalOptions = {
        productId: null,
        priceHolderSelector: '.price-box', //data-role="priceBox"
        optionsSelector: '.product-custom-option',
        optionConfig: {},
        optionHandlers: {},
        optionTemplate: '<%= data.label %>' +
            '<% if (data.finalPrice.value > 0) { %>' +
            ' +<%- data.finalPrice.formatted %>' +
            '<% } else if (data.finalPrice.value < 0) { %>' +
            ' <%- data.finalPrice.formatted %>' +
            '<% } %>',
        controlContainer: 'dd'
    };

    $.widget('mage.priceGrouped', {
        options: globalOptions,

        /**
         * @private
         */
        _init: function initPriceGroup() {
            $(this.options.optionsSelector, this.element).trigger('change');
        },

        /**
         * Widget creating method.
         * Triggered once.
         * @private
         */
        _create: function createPriceOptions() {
            var form = this.element,
                options = $(this.options.optionsSelector, form),
                priceBox = $(this.options.priceHolderSelector, $(this.options.optionsSelector).element);

            if (priceBox.data('magePriceBox') &&
                priceBox.priceBox('option') &&
                priceBox.priceBox('option').priceConfig
            ) {
                if (priceBox.priceBox('option').priceConfig.optionTemplate) {
                    this._setOption('optionTemplate', priceBox.priceBox('option').priceConfig.optionTemplate);
                }
                this._setOption('priceFormat', priceBox.priceBox('option').priceConfig.priceFormat);
            }

            options.on('change', this._onOptionChanged.bind(this));
        },

        /**
         * Custom option change-event handler
         * @param {Event} event
         * @private
         */
        _onOptionChanged: function onOptionChanged(event) {
            var changes = {},
                option = $(event.target);

            option.data('optionContainer', option.closest(this.options.controlContainer));

            var key = option.attr('name');
            var value = option.val();
            var price = this.options.optionConfig[key];
            changes[key] = {
                'basePrice': {
                    'amount': price * value
                },
                'finalPrice': {
                    'amount': price * value
                },
                'oldPrice': {
                    'amount': price * value
                },
                'originFinalPrice': {
                    'amount': price * value
                }
            };
            $(this.options.priceHolderSelector).trigger('updatePrice', changes);
        },
    });

    return $.mage.priceGrouped;
});
