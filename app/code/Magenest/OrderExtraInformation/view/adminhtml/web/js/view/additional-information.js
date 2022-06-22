/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([ // jshint ignore:line
    'jquery',
    "underscore",
    'uiComponent',
    'ko',
    'Magento_Ui/js/modal/modal',
    'mage/template',
    'mage/translate',
    'mage/validation'
], function ($, _, Component, ko, modal, mageTemplate, st) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magenest_OrderExtraInformation/additional-information',
            additionalData: null,
            actionUrl: null,
            formElement: '#additional-information-form'
        },
        editMode: ko.observable(false),
        customerNote: ko.observable(null),
        deliveryTime: ko.observable(null),
        saveVatInvoice: ko.observable(false),
        companyName: ko.observable(null),
        companyAddress: ko.observable(null),
        companyEmail: ko.observable(null),
        taxCode: ko.observable(null),
        isWholesale: ko.observable(null),
        isWholesaleOrder: ko.observable(null),
        telephoneCustomerConsign: ko.observable(null),
        options: [
            'In office hours',
            'Outside office hours',
        ],

        /**
         * Initialize
         *
         * @returns {exports.initialize}
         */
        initialize: function () {
            this._super();

            // ko.bindingHandlers.datepicker = {
            //     init: function (element) {
            //         $(element).calendar({dateFormat: $.datepicker.W3C, minDate: '+1'});
            //     }
            // };

            this.customerNote(this.additionalData['customer_note']);
            this.deliveryTime(this.additionalData['delivery_time']);
            this.companyName(this.additionalData['company_name']);
            this.taxCode(this.additionalData['tax_code']);
            this.companyAddress(this.additionalData['company_address']);
            this.companyEmail(this.additionalData['company_email']);
            this.telephoneCustomerConsign(this.additionalData['telephone_customer_consign']);
            if (this.additionalData['is_wholesale_order']) {
                this.isWholesale("Yes");
                this.isWholesaleOrder(true);
            } else {
                this.isWholesale("No");
                this.isWholesaleOrder(false);
            }
            if (this.companyName()) {
                this.saveVatInvoice(true);
            }

            return this;
        },

        /**
         * Submit form
         */
        submit: function () {
            var form = $(this.formElement);

            if (form.validation() && form.validation('isValid')
            ) {
                form.submit();
            }
        },

        /**
         * Open edit form
         */
        openEditForm: function () {
            this.editMode(true);
        },

        /**
         * Close edit form
         */
        closeEditForm: function () {
            this.editMode(false);
        },

        getDeliveryTime: function () {
            return st(this.options[this.deliveryTime()]);
        }
    });
});
