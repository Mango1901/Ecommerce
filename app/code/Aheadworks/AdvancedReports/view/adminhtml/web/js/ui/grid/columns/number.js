/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'Magento_Catalog/js/price-utils',
    'Magento_Ui/js/grid/columns/column'
], function (priceUtils, Column) {
    'use strict';

    return Column.extend({
        /**
         * Meant to preprocess data associated with a current columns' field
         *
         * @returns {String}
         */
        getLabel: function () {
            var number = this._super();

            return (number === null || typeof(number) == 'undefined') ? 0 : String(number * 1);
        }
    });
});
