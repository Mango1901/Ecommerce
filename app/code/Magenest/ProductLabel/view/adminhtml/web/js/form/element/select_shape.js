/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ProductLabel
 */
define([
    'underscore',
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (_, $, uiRegistry, Element) {
    'use strict';

    return Element.extend({

        initialize: function () {
            this._super();

            return this;
        }
    });
});
