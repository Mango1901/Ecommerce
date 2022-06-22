/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license sliderConfig is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

define([
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/form/element/single-checkbox'
], function ($, registry, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            allowAmountRange: false,
            listens: {
                'allowAmountRange': 'toggleElement'
            }
        },

        /**
         * @returns {exports.initialize}
         */
        initialize: function () {
            this._super();

            $('body').on(
                'click',
                'div[data-index="store-credit-information"] > .fieldset-wrapper-title' ,
                function () {
                    $('[data-index="container_min_credit"] > legend.admin__field-label').css('visibility', 'hidden');
                }
            );

            return this;
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super().observe('allowAmountRange');

            $('[data-index="container_min_credit"] > legend.admin__field-label').css('visibility', 'hidden');

            this.disableField(['min_credit', 'max_credit']);
            this.enableField(['credit_amount']);

            return this;
        },

        /**
         * @param {Array} fields
         */
        disableField: function (fields) {
            var self = this,
                parent = registry.get(this.parentName),
                root = registry.get(parent.parentName).name;

            registry.async(root + '.container_min_credit')(function (elem) {
                elem.visible(self.allowAmountRange());
            });

            $.each(fields, function (index, field) {
                registry.async(root + '.container_min_credit' + '.' + field)(function (elem) {
                    elem.visible(self.allowAmountRange());
                    elem.disabled(!self.allowAmountRange());
                });
            });

            $('[data-index="container_min_credit"] > legend.admin__field-label').css('visibility', 'hidden');
        },

        /**
         * @param {Array} fields
         */
        enableField: function (fields) {
            var self = this,
                parent = registry.get(this.parentName),
                root = registry.get(parent.parentName).name;

            $.each(fields, function (index, field) {
                registry.async(root + '.container_' + field + '.' + field)(function (elem) {
                    elem.visible(!self.allowAmountRange());
                    elem.disabled(self.allowAmountRange());
                });
            });
        },

        /**
         * Toggle element
         */
        toggleElement: function () {
            this.disableField(['min_credit', 'max_credit']);
            this.enableField(['credit_amount']);
        }
    });
});

