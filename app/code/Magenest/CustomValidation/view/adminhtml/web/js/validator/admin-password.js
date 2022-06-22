define([
    'jquery',
    'mage/translate',
    'jquery/validate'
], function ($) {
    return function (validator) {
        validator.addRule(
            'validate-admin-password',
            function (value) {
                if (value == null) {
                    return false;
                }

                let pass = $.trim(value);
                return pass.length === 0 || /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(pass);
            },
            $.mage.__('Please enter 8 or more characters; using at least one uppercase letter, one lowercase letter, one number and one special character.')
        );
        return validator;
    }
});
