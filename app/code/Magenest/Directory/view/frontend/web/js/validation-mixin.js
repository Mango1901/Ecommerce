define(['jquery', 'mageUtils', 'mage/translate'], function($, utils) {
    'use strict';

    return function() {
        $.validator.addMethod(
            'validate-specific-address',
            function(value, element) {
                return !utils.isEmpty(value);
            },
            $.mage.__('Please input specific address.')
        )
    }
});

