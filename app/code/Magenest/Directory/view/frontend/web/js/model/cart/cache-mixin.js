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

    return function (cache) {
        /**
         * Pick object by keys
         *
         * @param object
         * @returns {{}|*}
         */
        var pick = function (object) {
            var result = _.pick(object, ['countryId', 'region', 'regionId', 'postcode']);

            if (!!object) {
                var city = _.find(object['customAttributes'], function (attribute) {
                    return attribute['attribute_code'] == 'city_id';
                });
                if (city) {
                    result['city_id'] = city['value'];
                }
                var district = _.find(object['customAttributes'], function (attribute) {
                    return attribute['attribute_code'] == 'district_id';
                });
                if (district) {
                    result['district_id'] = district['value'];
                }
                var ward = _.find(object['customAttributes'], function (attribute) {
                    return attribute['attribute_code'] == 'ward_id';
                });
                if (ward) {
                    result['ward_id'] = ward['value'];
                }
            }

            return result;
        };

        return _.extend(cache, {
            _isAddressChanged: function (address) {
                return JSON.stringify(pick(this.get('address'))) !== JSON.stringify(pick(address));
            }
        })
    };
});
