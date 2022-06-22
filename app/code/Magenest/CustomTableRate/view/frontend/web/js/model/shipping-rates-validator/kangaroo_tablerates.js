/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mageUtils',
    '../shipping-rates-validation-rules/kangaroo_tablerates',
    'mage/translate'
], function ($, utils, validationRules, $t) {
    'use strict';

    return {
        validationErrors: [],

        /**
         * Validate
         *
         * @param {Object} address
         * @return {Boolean}
         */
        validate: function (address) {
            var self = this;

            this.validationErrors = [];
            $.each(validationRules.getRules(), function (field, rule) {
                if (rule.required && utils.isEmpty(address[field])) {
                    self.validationErrors.push($t('Field ') + field + $t(' is required.'))
                }
            });

            return !this.validationErrors.length;
        }
    };
});
