require([
    'jquery',
    'mage/translate',
    'jquery/validate'
    ], function($){
        $.validator.addMethod(
            'validate-zalo-window-size',
            function (v) {
                if ($.mage.isEmptyNoTrim(v)) {
                    return true;
                }
                return (!isNaN(v) && v >= 300 && v <= 500);
            },
            $.mage.__('The number must be in range from 300 to 500'));
    }
);