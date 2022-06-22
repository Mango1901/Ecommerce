define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'Magento_Customer/js/view/customer'
], function ($, customerData) {
    'use strict';

    let mixin = {
        nickname: function () {
            if (customerData.get('customer')().fullname) {
                return customerData.get('customer')().fullname;
            }

            return this._super();
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
