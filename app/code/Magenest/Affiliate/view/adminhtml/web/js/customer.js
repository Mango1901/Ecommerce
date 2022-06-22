
define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function ($, modal, $t) {
    "use strict";

    $.widget('magenest.customer', {
        options: {
            url: '',
            prefix: ''
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
            var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: $t('Select Customer'),
                buttons: []
            };
            modal(options, $('#customer-grid'));
            $('#customer-grid').modal('openModal');
        },

        /**
         * Init select customer
         */
        selectCustomer: function () {
            var self = this;
            $('body').delegate('#customer-grid_table tbody tr', 'click', function () {
                $("#" + self.options.prefix + "_customer_id").val($(this).find('input').val().trim());
                $("#" + self.options.prefix + "_customer_email").val($(this).find('td:nth-child(5)').text().trim());
                $("#" + self.options.prefix + "_current_balance").text($(this).find('td:nth-child(6)').text().trim());
                $(".action-close").trigger('click');
            });
        },

        /**
         * Init customer grid
         */
        initCustomerGrid: function () {
            var self = this;

            $("#" + this.options.prefix + "_customer_email").click(function () {
                $.ajax({
                    method: 'POST',
                    url: self.options.url,
                    data: {form_key: window.FORM_KEY, action: self.options.action},
                    showLoader: true
                }).done(function (response) {
                    $('#customer-grid').html(response);
                    self.initPopup();
                });
            });
        }
    });

    return $.magenest.customer;
});

