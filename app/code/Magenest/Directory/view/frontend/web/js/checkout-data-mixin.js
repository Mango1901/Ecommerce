/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'mage/translate'
], function ($, _) {
    'use strict';

    return function (checkoutData) {
        var originFunction = checkoutData.setShippingAddressFromData.bind(checkoutData),
            output;

        return _.extend(checkoutData, {
            setShippingAddressFromData: function (data) {
                data['custom_attributes'] = {};
                output = originFunction(data);
            }
        })
    };
});