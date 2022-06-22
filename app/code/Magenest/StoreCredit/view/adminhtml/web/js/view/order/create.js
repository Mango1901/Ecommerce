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

define(['jquery', 'Magento_Sales/order/create/form'], function ($) {
    "use strict";

    $('body').on('change', '.mp-store-credit', function () {
        window.order.loadArea(['totals', 'billing_method'], true, {'mp_spending_credit': !!$(this).is(':checked')});
    });
});
