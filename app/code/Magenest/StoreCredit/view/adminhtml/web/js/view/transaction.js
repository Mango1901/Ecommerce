/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */
define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function ($, modal, $t) {
    "use strict";

    $.widget('magenest.transaction', {
        options: {
            url: ''
        },
        isloaded: false,

        /**
         * This method constructs a new widget.
         * @private
         */
        _create: function () {
            this.initCustomerGrid();
            this.selectCustomer();
        },

        /**
         * Init popup
         * Popup will automatic open
         */
        initPopup: function () {
            var grid = $('#customer-grid'),
                options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    title: $t('Select Customer'),
                    buttons: []
                };
            modal(options, grid);
            grid.modal('openModal');
        },

        /**
         * Init select customer
         */
        selectCustomer: function () {
            $('body').delegate('#customer-grid_table tbody tr', 'click', function () {
                $("#customer_id_form").val($(this).find('input').val().trim());
                $("#customer_email").val($(this).find('td:nth-child(5)').text().trim());
                $(".action-close").trigger('click');
            });
        },

        /**
         * Init customer grid
         */
        initCustomerGrid: function () {
            var self = this;

            $("#customer_email").on('click', function () {
                $.ajax({
                    method: 'POST',
                    url: self.options.url,
                    data: {formKey: window.FORM_KEY},
                    showLoader: true
                }).done(function (response) {
                    $('#customer-grid').html(response);
                    self.initPopup();
                });
            });
        }
    });

    return $.magenest.transaction;
});

