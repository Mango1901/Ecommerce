/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license that is
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
    'ko',
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'mage/translate'
], function (ko, $, Component, customerData) {
    'use strict';

    var config = customerData.get('customer'),
        isReload = true;

    return Component.extend({
        label: ko.computed(function () {
            var balance = config().convertedBalance || '',
                isEnabledFor = config().isEnabledFor || false;

            if (isEnabledFor) {
                return $.mage.__('Your Balance') + ' ' + balance;
            }

            return '';
        }),

        initialize: function () {
            this._super();

            if (isReload) {
                customerData.reload(['customer'], false).done(function () {
                    config = customerData.get('customer');
                });
                isReload = false;
            }
        }
    });
});
