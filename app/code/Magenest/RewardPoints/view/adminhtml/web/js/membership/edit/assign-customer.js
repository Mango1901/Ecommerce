/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global $, $H */

define([
    'jquery',
    'mage/adminhtml/grid'
], function ($) {
    'use strict';

    return function (config) {
        var selectedCustomers = config.selectedCustomers,
            eventCustomers = $H(selectedCustomers),
            gridJsObject = window[config.gridJsObjectName];

        $('in_group_customers').value = Object.toJSON(eventCustomers);

        /**
         * Register Event Customer
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerEventCustomer(grid, element, checked) {
            if (checked) {
                eventCustomers.set(element.value, element.value);

            } else {
                eventCustomers.unset(element.value);
            }
            $('in_group_customers').value = Object.toJSON(eventCustomers);
        }

        $('body').on('change', '#membership_customer_table > tbody > tr input[name="customer-checkbox"]', function (e) {
            registerEventCustomer(gridJsObject, this, this.checked);
        });
    };
});
