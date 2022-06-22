/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'Magento_Catalog/js/price-utils',
    'Aheadworks_AdvancedReports/js/ui/grid/columns/number'
], function (priceUtils, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            imports: {
                priceFormat: '${ $.provider }:data.priceFormat',
            }
        },

        /**
         * Meant to preprocess data associated with a current columns' field
         *
         * @returns {String}
         */
        getLabel: function () {
            var price = this._super();

            return priceUtils.formatPrice(price, this.priceFormat);
        }
    });
});
