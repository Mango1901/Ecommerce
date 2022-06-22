/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'mage/utils/wrapper',
    'uiRegistry'
], function ($, _, wrapper, uiRegistry) {
    'use strict';

    return function (payloadExtender) {

        return wrapper.wrap(payloadExtender, function (originalAction, payload) {
            originalAction(payload);
            payload.addressInformation['extension_attributes'] = $.extend(
                true,
                {},
                payload.addressInformation['extension_attributes'],
                uiRegistry.get('checkoutProvider').get('additional-data')
            );

            return payload;
        });
    };
});