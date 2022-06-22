var config = {
    config: {
        mixins: {
            'Magento_Ui/js/lib/validation/validator': {
                'Magenest_CustomValidation/js/validator/admin-password': true
            },
            'mage/validation': {
                'Magenest_CustomValidation/js/validator/jquery/admin-password': true
            }
        }
    }
};
