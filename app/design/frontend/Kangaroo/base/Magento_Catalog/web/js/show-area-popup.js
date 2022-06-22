/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
], function ($) {
    'use strict';

    return function (widget) {

        $.widget('mage.catalogAddToCart', widget, {
            /**
             * @param {jQuery} form
             */
            submitForm: function (form) {
                if (!$.cookie('area_code')) {
                    $('.header-store-menu').modal('openModal');
                } else {
                    this._super(form);
                }
            },
        });

        return $.mage.catalogAddToCart;
    };
});
