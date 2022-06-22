define([
    'jquery'
], function ($) {
    "use strict";

    return function () {

        var validateFunction = function (value) {
            var mob_regex = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
            var email_regex = /^([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/;
            if (!$.mage.isEmptyNoTrim(value))
            {
                if (email_regex.test(value) && !mob_regex.test(value)) {
                    return true;
                } else return mob_regex.test(value) && !email_regex.test(value);
            } else {
                return false;
            }
        };

        $.validator.addMethod(
            'validate-custom-mob-email-rule',
            validateFunction,
            $.mage.__('Please enter a valid email address or a valid phone number')
        );

        $.validator.addMethod(
            'validate-custom-mob-checkout-email-rule',
            validateFunction,
            $.mage.__('Please enter a valid email address')
        );
    }
});
