define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'mage/url',
    'loaderAjax'
], function ($, customerData, urlBuilder) {
    'use strict';

    return function (widget) {
        $.widget('mage.addToWishlist', widget, {

            /**
             * Custom wishlist submission via AJAX
             * @param {jQuery.Event} event
             */
            _validateWishlistQty: function (event) {
                event.preventDefault();
                event.stopPropagation();

                let qty = $(this.options.qtyInfo), element = $(event.currentTarget);
                if ((qty.validation() && qty.validation('isValid'))) {
                    if (element.hasClass('active')) {
                        this._removeWishlist(element);
                    } else {
                        this._addWishlist(element);
                    }
                }
            },

            _addWishlist: function (element) {
                let post = JSON.parse(element.attr('data-post'));
                post.data.form_key = $.cookie('form_key');

                $('body').loader('show');
                $.ajax({
                    url: post.action,
                    type: 'POST',
                    data: post.data,
                    complete: function () {
                        $('body').loader('hide');
                    },
                    success: response => {
                        element.addClass('active');
                        element.attr('data-remove', JSON.stringify({'item': response.id}));
                        customerData.reload(['messages'], true);
                    },
                    error: function (response) {
                        console.log(response);
                        customerData.reload(['messages'], true);
                    }
                });
            },

            _removeWishlist: function (element) {
                let post = JSON.parse(element.attr('data-remove'));
                post.form_key = $.cookie('form_key');
                $('body').loader('show');
                $.ajax({
                    url: urlBuilder.build('wishlist/index/remove'),
                    type: 'POST',
                    data: post,
                    complete: function () {
                        $('body').loader('hide');
                    },
                    success: () => {
                        element.removeClass('active');
                        customerData.reload(['messages'], true);
                    },
                    error: function (response) {
                        console.log(response);
                        customerData.reload(['messages'], true);
                    }
                });
            }
        });

        return $.mage.addToWishlist;
    }
});
