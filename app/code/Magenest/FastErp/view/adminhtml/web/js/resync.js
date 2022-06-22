/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function ($) {
    'use strict';

    $.widget('mage.orderResyncDialog', {
        options: {
            url:     null,
            message: null,
            modal:  null,
            title: null,
        },

        // /**
        //  * @protected
        //  */
        // _create: function () {
        //     this._prepareDialog();
        // },

        /**
         * Show modal
         */
        showDialog: function () {
            if (!this.options.dialog) {
                this._prepareDialog();
            }
            this.options.dialog.html(this.options.message).modal('openModal');
        },

        /**
         * Redirect to edit page
         */
        redirect: function () {
            window.location = this.options.url;
        },

        /**
         * Prepare modal
         * @protected
         */
        _prepareDialog: function () {
            var self = this;

            this.options.dialog = $('<div class="ui-dialog-content ui-widget-content"></div>').modal({
                type: 'popup',
                modalClass: 'edit-order-popup',
                title: $.mage.__(self.options.title),
                buttons: [{
                    text: $.mage.__('Ok'),
                    'class': 'action-primary',

                    /** @inheritdoc */
                    click: function () {
                        self.redirect();
                    }
                }]
            });
        }
    });

    return $.mage.orderResyncDialog;
});
