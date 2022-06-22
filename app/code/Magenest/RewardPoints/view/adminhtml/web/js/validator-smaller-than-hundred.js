require(
    [
        'uiRegistry',
        'mageUtils',
        'Magento_Ui/js/lib/validation/validator',
        'jquery',
        'mage/translate'
    ], function (uiRegistry, utils, validator, $) {

        validator.addRule(
            'validate-smaller-than-hundred',
            function (value) {
                var source = uiRegistry.get('rewardpoints_membership_edit.rewardpoints_membership_edit_data_source');

                var validateResult = true;

                if (source.data.membership.added_value_type === "2") {
                    if (parseInt(value) > 100) {
                        validateResult = false;
                    }
                }


                return validateResult;
            }
            , $.mage.__('You cannot input a number greater than 100.')
        );
    });