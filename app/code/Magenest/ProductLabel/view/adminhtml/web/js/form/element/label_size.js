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
                'value': 'labelSize'
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

        labelSize: function (value) {
            var self = this;
            if (value) {
                self.sizeDefault = value;
            }
        },

        clickLabelSize: function () {
            var self = this;
            var rangeSlider = function(){
                var slider = $('.range-slider'),
                    range = $('.range-slider__range'),
                    valueRange = $('.range-slider__value');

                slider.each(function(){
                    valueRange.each(function(){
                        var valueChange = $(this).prev().attr('value');
                        self.value(valueChange);
                        $(this).html(valueChange);
                    });

                    range.on('input', function(){
                        $(this).next(valueRange).html(this.value);
                        range.val($(this).val());
                        this.sizeDefault = $(this).val();
                        uiRegistry.get('labelPreview').changeLabelSizeValue($(this).val());
                    });
                });
            };
            rangeSlider();
        },
    });
});
