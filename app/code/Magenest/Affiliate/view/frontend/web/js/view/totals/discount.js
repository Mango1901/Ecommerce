
/*global define*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, totals) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magenest_Affiliate/totals/summary/discount'
            },
            getTotal: function () {
                return totals.getSegment('affiliate_discount');
            },
            getValue: function () {
                return this.getFormattedPrice(this.getTotal().value);
            },
            isCalculated: function () {
                return this.getTotal();
            }
        });
    }
);
