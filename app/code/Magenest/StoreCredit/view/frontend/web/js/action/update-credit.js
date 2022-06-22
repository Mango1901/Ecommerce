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

define(
    [
        'jquery',
        'underscore',
        'mage/storage',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/action/get-payment-information',
        'Magenest_StoreCredit/js/model/resource-url-manager'
    ],
    function ($,
              _,
              storage,
              quote,
              totals,
              errorProcessor,
              getPaymentInformationAction,
              resourceUrlManager
    ) {
        'use strict';

        return function (amount) {
            totals.isLoading(true);
            return storage.post(
                resourceUrlManager.getUrlForSpending(),
                JSON.stringify({amount})
            ).done(function (response) {
                quote.setTotals(response);

                var deferred = $.Deferred();
                getPaymentInformationAction(deferred);
                $.when(deferred).done(function () {
                    totals.isLoading(false);
                });
            }).fail(function (response) {
                errorProcessor.process(response);
                totals.isLoading(false);
            });
        };
    }
);
