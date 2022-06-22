define([
    'jquery',
    'mageUtils',
    'mage/translate',
    'jquery/validate'
], function($, utils) {
    'use strict';

    return function(target) {
        let message = "";

        $.validator.addMethod(
            'validate-telephone-require',
            function (value) {
                // value = value.replace(/\s+/g, '');
                if (/\s/.test(value)) {
                    message = $.mage.__("Please check for exceeded space in phone number");
                    return false;
                }
                message = $.mage.__("Please specify a valid phone number");
                return !utils.isEmpty(value) && value.length >= 10 && value.length <= 11 &&
                    value.match(/^0(9|8[7|6|8|1|2|3|4|5|9]|7[0|6|7|8|9]|5[2|6|8|9]|2|3)+([0-9]{7,9})$\b/);
            },
            function () {
                return message;
            }
        );

        return target;
    }
});
