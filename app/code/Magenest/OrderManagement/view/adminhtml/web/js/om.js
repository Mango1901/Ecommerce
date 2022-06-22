/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/modal',
    'mage/dataPost',
    'mageUtils',
    'underscore',
    'mage/template',
    'text!Magenest_OrderManagement/template/confirm.html',
    'jquery/ui',
    'mage/validation',
    'mage/mage',
    'mage/backend/form',
    'mage/calendar',
    'jquery/validate'
], function ($, confirm, modal, dataPost, utils, _, mageTemplate, confirmTemplate) {
    'use strict';

    $.widget('mage.om', {
        options: {
            confirmDialog: null,
            confirmForm: null
        },

        /**
         * @private
         */
        _create: function () {
            var self = this;
            this._super();

            return this;
        },

        /**
         * Show confirm dialog
         *
         * @param message
         * @param action
         * @param warehouse
         */
        showConfirmDialog: function (message, action, warehouse) {
            warehouse = JSON.parse(warehouse);
            var self = this;

            if(!this.options.confirmDialog){
                this.options.confirmDialog =  modal({
                    title: $.mage.__(message),
                    innerScroll: true,
                    modalClass: '_image-box',
                    buttons: [{
                        text: $.mage.__('Confirm'),
                        class: 'action primary action-hide-popup',
                        click: function () {
                            if (self.options.confirmForm.validation() && self.options.confirmForm.validation('isValid')) {
                                self.options.confirmForm.submit();
                                this.closeModal();
                            }
                        }
                    }],
                    opened: function () {
                        self.options.confirmForm = $('#om-confirm-form').attr('action', action);
                    },
                    clickableOverlay: true
                }, $('<div/>').html(mageTemplate(confirmTemplate, {warehouse: warehouse})));
            }

            this.options.confirmDialog.openModal();
        },

        /**
         * Show supplier action dialog
         *
         * @param message
         * @param action
         */
        showSupplierActionDialog: function (message, action) {
            confirm({
                title: $.mage.__('Attention'),
                content: $.mage.__(message),
                buttons: [{
                    text: $.mage.__('Reject'),
                    class: 'action-secondary',
                    click: function (event) {
                        this.closeModal(event, true);
                        utils.submit({
                            url: action,
                            data: {
                                is_confirmed: false
                            }
                        }, {});
                        $('body').trigger('processStart');
                    }
                }, {
                    text: $.mage.__('Confirm'),
                    class: 'action-primary',
                    click: function (event) {
                        utils.submit({
                            url: action,
                            data: {
                                is_confirmed: true
                            }
                        }, {});
                        $('body').trigger('processStart');
                    }
                }]
            });
        },

        /**
         * Show supplier returned dialog
         *
         * @param message
         * @param action
         */
        showReturnedDialog: function (message, action) {
            confirm({
                title: $.mage.__('Attention'),
                content: $.mage.__(message),
                buttons: [{
                    text: $.mage.__('Confirm, but something went wrong'),
                    class: 'action-secondary'
                }, {
                    text: $.mage.__('Confirm'),
                    class: 'action-primary',
                    click: function (event) {
                        this.closeModal(event, true);
                    }
                }],
                actions: {
                    confirm: function () {
                        this.isConfirmed = true;
                    },
                    cancel: function () {
                        this.isConfirmed = false;
                    },
                    always: function () {
                        utils.submit({
                            url: action,
                            data: {
                                is_confirmed: this.isConfirmed
                            }
                        }, {});
                    }
                }
            });
        },

        /**
         * Show dialog
         *
         * @param message
         * @param action
         * @param submitBtn
         * @param cancelBtn
         */
        showDialog: function (message, action, submitBtn, cancelBtn){
            if (submitBtn == undefined) {
                submitBtn = $.mage.__('OK');
            }

            if (cancelBtn == undefined) {
                cancelBtn = $.mage.__('Cancel');
            }

            confirm({
                title: $.mage.__('Attention'),
                content: message,
                actions: {
                    confirm: function () {
                        dataPost().postData({
                            data: {
                                form_key: window.FORM_KEY
                            },
                            action: action
                        });
                    }
                },
                buttons: [{
                    text: cancelBtn,
                    class: 'action-secondary action-dismiss',
                    click: function (event) {
                        this.closeModal(event);
                    }
                }, {
                    text: submitBtn,
                    class: 'action-primary action-accept',
                    click: function (event) {
                        this.closeModal(event, true);
                    }
                }]
            });
        }
    });

    return $.mage.om;
});
