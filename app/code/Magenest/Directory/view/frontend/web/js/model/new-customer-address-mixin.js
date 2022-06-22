/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'mage/utils/wrapper'
], function ($, _, wrapper) {
    'use strict';

    return function (newCustomerAddress) {

        return wrapper.wrap(newCustomerAddress, function (originalAction, addressData) {
            if (!addressData.hasOwnProperty('custom_attributes') || !addressData['custom_attributes'].length) {
                addressData['custom_attributes'] = {};
            }

            _.each(['city_id', 'district', 'district_id', 'ward', 'ward_id'], function (attribute) {
                if (addressData.hasOwnProperty(attribute)) {
                    addressData['custom_attributes'][attribute] = {
                        'attribute_code': attribute,
                        'value': addressData[attribute]
                    };
                }
            });

            return originalAction(addressData);
        });
    };
});