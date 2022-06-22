/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/template',
    'underscore',
    'jquery/ui',
    'jquery/validate',
    'mage/validation'
], function ($, modal, mageTemplate, _) {
    'use strict';

    $.widget('mage.vatInvoice', {
        options: {
            formBlockTemplate: '<div class="box vat-invoice-form">'
            + '<form id="vat-invoice-form" method="post" action="<%- saveUrl %>">'
            + '<div class="box-content">'
            + '<fieldset class="fieldset">'
            + '<input name="form_key" type="hidden" value="<%- formKey %>">'
            + '<div class="field field-company-name required">'
            + '<label class="label" for="company-name"><span>Tên công ty</span></label>'
            + '<div class="control">'
            + '<input type="text" id="company-name" value="<%- company_name %>" name="data[company_name]" title="Company Name" class="input-text required-entry">'
            + '</div></div>'
            + '<div class="field field-tax-code required">'
            + '<label class="label" for="tax-code"><span>Mã số thuế</span></label>'
            + '<div class="control">'
            + '<input type="text" id="tax-code" value="<%- tax_code %>" name="data[tax_code]" title="Tax Code" class="input-text required-entry">'
            + '</div></div>'
            + '<div class="field field-company-address required">'
            + '<label for="company-address" class="label"><span>Địa chỉ công ty</span></label>'
            + '<div class="control">'
            + '<input type="text" name="data[company_address]" id="company_address" value="<%- company_address %>" title="Company Address" class="input-text required-entry">'
            + '</div></div>'
            + '<div class="field field-company-email required">'
            + '<label for="company-email" class="label"><span>Email công ty</span></label>'
            + '<div class="control">'
            + '<input type="email" name="data[company_email]" id="company_email" value="<%- company_email %>" title="Company Email" class="input-text required-entry">'
            + '</div></div>'
            + '</fieldset >'
            + '<div class="box-actions">'
            + '<% if(has_default_information) { %>'
            + '<button type="button" class="action secondary cancel" title="Cancel">Hủy bỏ</button>'
            + '<% } %>'
            + '<button type="button" class="action primary save" title="Save">Lưu lại</button>'
            + '</div>'
            + '</form>',
            formOptions: {}
        },

        /**
         * @private
         */
        _create: function () {
            var self = this,
                form;

            this.element.find('.box-actions .action-create').on('click', function () {
                if (!self.element.next().length) {
                    self.element.after(mageTemplate(self.options.formBlockTemplate, self.options.formOptions));

                    form = $('#vat-invoice-form');
                    form.find('.action.save').click(function () {
                        if (form.validation() && form.validation('isValid')) {
                            form.submit();
                        }
                    });
                    form.find('.action.cancel').click(function () {
                        self.element.next().remove();
                    });
                }
            });

            this.element.find('.box-actions .action-remove').on('click', function () {
                location.href = self.options.formOptions['removeUrl'];
            });

            return this;
        }
    });

    return $.mage.vatInvoice;
});
