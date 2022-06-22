/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-rates-validation-rules',
    '../../model/shipping-rates-validator/kangaroo_tablerates',
    '../../model/shipping-rates-validation-rules/kangaroo_tablerates'
], function (
    Component,
    defaultCustomTableRateRatesValidator,
    defaultCustomTableRateRatesValidationRules,
    shippingRatesValidator,
    shippingRatesValidationRules
) {
    'use strict';

    defaultCustomTableRateRatesValidator.registerValidator('kangaroo_tablerates', shippingRatesValidator);
    defaultCustomTableRateRatesValidationRules.registerRules('kangaroo_tablerates', shippingRatesValidationRules);

    return Component;
});
