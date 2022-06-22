define([
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
], function ($, Component, customerData) {
    'use strict';

    return Component.extend({
        namespace  : "product-purchased-customer",

        initialize: function (config) {
            this._super();
            var self = this;
            customerData.reload([self.namespace], true);

            $(document).on('customer-data-reload', function (event, sections) {
                if ((_.isEmpty(sections) || _.contains(sections, self.namespace))) {
                    const listProductPurchased = customerData.get(self.namespace)()['listProductIdPurchased'];
                    self.hideForm(listProductPurchased, config.currentProductId)
                }
            });
        },

        hideForm: function (listProductPurchased, currentProductId) {
            if (!listProductPurchased || Object.values(listProductPurchased).indexOf(String(currentProductId)) === -1) {
                $('.review-add').remove();
            } else {
                $('.review-add').show();
            }
        }
    });
});
