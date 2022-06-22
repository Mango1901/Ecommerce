/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ProductLabel
 */
define([
    'jquery',
    'ko',
    'Magento_Ui/js/form/element/abstract',
    'uiRegistry',
], function ($, ko, Abstract, uiRegistry) {
    'use strict';
    return Abstract.extend({

        defaults: {
            sizeDefault: '80',
            listens: {
                'value': 'processLabel'
            },
        },

        initialize: function () {
            this._super();
            return this;
        },

        initObservable: function () {
            this._super().observe([
                'sizeDefault',
            ]);
            return this;
        },
        processLabel: function (value) {
            var self = this;
            if (value) {
                self.sizeDefault = value;
            }
        },

        clickProductLabel: function () {
            var self = this;
            var rangeSlider = function(){
                var labelSize = $('.product_label_size'),
                    productRange = $('.product-range'),
                    productRangeValue = $('.product-range-value');

                labelSize.each(function(){
                    productRangeValue.each(function(){
                        var valueChange = $(this).prev().attr('value');
                        self.value(valueChange);
                        $(this).html(valueChange);
                    });

                    productRange.on('input', function(){
                        $(this).next(productRangeValue).html(this.value);
                        productRange.val($(this).val());
                        this.sizeDefault = $(this).val();
                        uiRegistry.get('labelPreview').changeLabelSizeValue($(this).val());
                    });
                });
            };
            rangeSlider();
        },
    });
});
