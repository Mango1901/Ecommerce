/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Label extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ProductLabel
 */
define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/checkbox-set',
], function (_, uiRegistry, checkbox) {
    'use strict';

    return checkbox.extend({

        defaults: {
            showShape: false,
            showShapeColor: false,
            showImage: false,
            listens: {
                'value': 'changeOptionValue',
                'visible': 'changeVisibility'
            },
            exports: {
                'showShape': '${ $.parentName }.shape_type:visible',
                'showShapeColor': '${ $.parentName }.shape_color:visible',
                'showImage': '${ $.parentName }.image:visible',
                'showText': '${ $.parentName }.text:visible',
                'showTextColor': '${ $.parentName }.text_color:visible',
                'showTextSize': '${ $.parentName }.text_size:visible',
                'showCustomCss': '${ $.parentName }.custom_css:visible',
            }
        },

        initObservable: function () {
            this._super().observe([
                'showShape',
                'showShapeColor',
                'showImage',
                'showText',
                'showTextColor',
                'showTextSize',
                'showCustomCss',
            ]);
            return this;
        },

        initialize: function () {
            var self = this;
            this._super();

            return this;
        },

        changeOptionValue: function (value) {
            var self = this;
            //value == 2 => Label Type = Shape
            //value == 3 => Label Type = Image
            self.showShape(value == 2);
            self.showShapeColor(value == 2);
            self.showImage(value == 3);

            //If you choose label type as image, you will hide config fields to enter text
            if (value == 3) {
                self.showText(false);
                self.showTextColor(false);
                self.showTextSize(false);
                self.showCustomCss(false);
            } else {
                self.showText(true);
                self.showTextColor(true);
                self.showTextSize(true);
                self.showCustomCss(true);
            }
        },

        changeVisibility: function (value) {
            var self = this;
            if (value == false) {
                self.showShape(false);
                self.showShapeColor(false);
                self.showImage(false);
            } else {
                self.changeOptionValue(self.value());
            }
        }
    });
});
