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

define(
    [
        'jquery',
        'Magento_Checkout/js/model/resource-url-manager'
    ],
    function ($, resourceUrlManager) {
        "use strict";
        return $.extend(resourceUrlManager, {
            /**
             * Get update spending credit url
             * @return {*|string}
             */
            getUrlForSpending: function () {
                var urls = {
                    'customer': '/mpStoreCredit/mine/spending'
                };
                return this.getUrl(urls, {});
            }
        });
    }
);

