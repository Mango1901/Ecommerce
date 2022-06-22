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
            'equalToPassword',
            function (value, element, param) {
                var target = $(param);
                if (this.settings.onfocusout) {
                    target.unbind(".validate-equalTo").bind("blur.validate-equalTo", function () {
                        $(element).valid();
                    });
                }
                return value === target.val();
            },
            $.mage.__("New password and confirmation password are not matched")
        );

        return target;
    }
});
