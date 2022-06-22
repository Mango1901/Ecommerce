/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [],
    function () {
        'use strict';
        return {
            getRules: function() {
                return {
                    'directory-information.city_id': {
                        'required': true
                    },
                    'directory-information.district_id': {
                        'required': true
                    },
                    'directory-information.ward_id': {
                        'required': true
                    },
                    // 'street': {
                    //     'required': true
                    // }
                };
            }
        };
    }
);
