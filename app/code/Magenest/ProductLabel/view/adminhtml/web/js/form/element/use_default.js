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
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/single-checkbox',
    'Magenest_ProductLabel/js/form/element/type_options'
], function (_, uiRegistry, Element, type_options) {
    'use strict';

    return Element.extend({

        defaults: {
            sameAsCategory: false,
            exports: {
                'field0': '!${$.parentName}.display:visible',
                'field1': '!${$.parentName}.type:visible',
                'field5': '!${$.parentName}.label_size:visible',
                'field6': '!${$.parentName}.position:visible',
                'field7': '!${$.parentName}.text:visible',
                'field8': '!${$.parentName}.text_color:visible',
                'field9': '!${$.parentName}.text_size:visible',
                'field10': '!${$.parentName}.custom_css:visible'
            },
            links: {
                'field0': 'sameAsCategory',
                'field1': 'sameAsCategory',
                'field5': 'sameAsCategory',
                'field6': 'sameAsCategory',
                'field7': 'sameAsCategory',
                'field8': 'sameAsCategory',
                'field9': 'sameAsCategory',
                'field10': 'sameAsCategory',

            }
        },

        initObservable: function () {
            this._super().observe([
                'sameAsCategory',
            ]);
            return this;
        },

        initialize: function () {
            var self = this;
            this._super();

            self.sameAsCategory(this.value() == 1);

            this.value.subscribe(function (value) {
                self.sameAsCategory(value == 1);
            }, self);

            return this;
        }
    });
});
