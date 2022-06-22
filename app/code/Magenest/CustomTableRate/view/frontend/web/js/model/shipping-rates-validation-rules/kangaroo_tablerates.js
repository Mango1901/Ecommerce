/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([], function () {
    'use strict';

    return {
        /**
         * Get rules
         *
         * @return {Object}
         */
        getRules: function () {
            return {
                'city_id': {
                    'required': true
                },
                'district_id': {
                    'required': true
                },
                'ward_id': {
                    'required': true
                }
            };
        }
    };
});
