require([
        'jquery',
        'mage/translate',
        'jquery/validate'],
    function($){
        $.validator.addMethod(
            'validate-size', function (s) {
                    if (!/[A-Za-z#@!%&='",_`~<>*+?^${}()\/\-|[\]\\]/g.test(s)){
                        return true;
                    }else{
                        return false;
                    }
            }, $.mage.__('Please enter a valid number in this field!')
        );
        $.validator.addMethod(
            'validate-title', function (t) {
                if (t.length <= 50){
                    return true;
                }else{
                    return false;
                }
            }, $.mage.__('Title must not be greater than 50 character length!')
        );
        $.validator.addMethod(
            'validate-content-button', function (c) {
                if (c.length <= 50){
                    return true;
                }else{
                    return false;
                }
            }, $.mage.__('Content must not be greater than 50 character length!')
        );
        $.validator.addMethod(
            'validate-time-number', function (time) {
                if (!/[A-Za-z#@!%&='",._`~<>*+?^${}()\/\-|[\]\\]/g.test(time)){
                    return true;
                }else{
                    return false;
                }
            }, $.mage.__('Please enter a valid number in this field!')
        );
    }
);