/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        'Magenest_GiaoHangTietKiem/js/model/shipping-rates-validator',
        'Magenest_GiaoHangTietKiem/js/model/shipping-rates-validation-rules'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        shippingRatesValidator,
        shippingRatesValidationRules
    ) {
        'use strict';
        defaultShippingRatesValidator.registerValidator('giaohangtietkiem', shippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('giaohangtietkiem', shippingRatesValidationRules);
        return Component;
    });
