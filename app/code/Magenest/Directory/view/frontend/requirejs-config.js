/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/new-customer-address': {
                'Magenest_Directory/js/model/new-customer-address-mixin': true
            },
            'Magento_Checkout/js/model/cart/cache': {
                'Magenest_Directory/js/model/cart/cache-mixin': true
            },
            'Magento_Checkout/js/model/address-converter': {
                'Magenest_Directory/js/model/address-converter-mixin': true
            },
            'Magento_Checkout/js/checkout-data': {
                'Magenest_Directory/js/checkout-data-mixin': true
            },
            'mage/validation': {
                'Magenest_Directory/js/validation-mixin': true
            }
        }
    },

    map: {
        '*': {
            'Magento_Customer/js/model/customer-addresses': 'Magenest_Directory/js/model/customer-addresses',
            'Magento_Checkout/js/view/shipping-address/address-renderer/default': 'Magenest_Directory/js/view/shipping-address/address-renderer/default',
            'Magento_Checkout/js/view/shipping-information/address-renderer/default': 'Magenest_Directory/js/view/shipping-information/address-renderer/default',
            'Magento_Checkout/js/view/billing-address': 'Magenest_Directory/js/view/billing-address'
        }
    }
};
