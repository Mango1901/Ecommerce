/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'mage/translate'
], function (Component, customerData, $, $t) {
    'use strict';

    return Component.extend({
        /** @inheritdoc */
        customerLoaded: false,

        initialize: function () {
            this._super();

            this.customer = customerData.get('customer');

            var self = this;
            var countLoad = 0;
            this.customer.subscribe(function (value) {
                self.customerLoaded = !!value.firstname;
                if (!self.customerLoaded && countLoad < 3) {
                    customerData.reload(['customer'], true);
                    countLoad++;
                }
            });

            $( document ).ready(function() {
                if ($.cookie('area_code')) {
                    $("#area-label").text(': ' + $.cookie('area_label'));
                    $("#" + $.cookie('area_code')).attr( 'checked', true );
                    $("#area-popup-modal .note").show();
                    $("#area-prefix").text($t('Area'));
                }
            });

        },

        showContent: function () {
            $('.greet-wrapper').css('opacity', 1);
        }
    });
});
