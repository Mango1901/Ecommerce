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

    return function (addressConverter) {
        var originFunction = addressConverter.quoteAddressToFormAddressData.bind(addressConverter),
            output;

        return _.extend(addressConverter, {
            quoteAddressToFormAddressData: function (addrs) {
                output = originFunction(addrs);

                if (!!addrs){
                    _.each(addrs['customAttributes'], function(attribute) {
                       output[attribute['attribute_code']] = attribute['value'];
                    });
                }

                return output;
            }
        })
    };
});