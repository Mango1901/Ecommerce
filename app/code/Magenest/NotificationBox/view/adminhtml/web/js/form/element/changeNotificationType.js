define([
    'jquery',
    'Magento_Ui/js/form/element/select'
], function ($, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            customName: '${ $.parentName }.${ $.index }_input'
        },
        /**
         * Change currently selected option
         *
         * @param {String} id
         */
        selectOption: function(id){
            var selectedValue = $("#"+id).val();
            console.log(selectedValue);
            if(selectedValue === 'order_status_update') {
                $('div[data-index="order_status"]').show();
                $('div[data-index="order_status_review"]').hide();
                $('div[data-index="set_abandoned_cart_time"]').hide();
            }
            else if(selectedValue === 'abandoned_cart_reminds') {
                $('div[data-index="order_status"]').hide();
                $('div[data-index="order_status_review"]').hide();
                $('div[data-index="set_abandoned_cart_time"]').show();
            }
            else if(selectedValue === 'review_reminders') {
                $('div[data-index="order_status"]').hide();
                $('div[data-index="order_status_review"]').show();
                $('div[data-index="set_abandoned_cart_time"]').hide();
            }
            else if(selectedValue !== 'order_status_update'
                && selectedValue !== 'abandoned_cart_reminds'
                && selectedValue !== 'review_reminders'
                && selectedValue !== undefined
                && selectedValue !== ''
            ){
                $('div[data-index="order_status"]').hide();
                $('div[data-index="order_status_review"]').hide();
                $('div[data-index="set_abandoned_cart_time"]').hide();
            }
        },
    });
});
