require(
    [
        'uiRegistry',
        'mageUtils',
        'Magento_Ui/js/lib/validation/validator',
        'jquery',
        'mage/translate'
    ], function (uiRegistry, utils, validator, $) {

        validator.addRule(
            'membership-sort-order',
            function (value) {
                var source = uiRegistry.get('rewardpoints_membership_edit.rewardpoints_membership_edit_data_source');
                var ajaxUrl = source.validate_order_url;

                var data = {'membershipId': source.data.membership.id, 'value': value, 'form_key': window.FORM_KEY};

                var validateResult = false;

                $.ajax({
                    url: ajaxUrl,
                    data: data,
                    dataType: 'json',
                    type: 'POST',
                    showLoader: true,
                    async: false,

                    /**
                     * Success callback.
                     * @param {Object} resp
                     * @returns {Boolean}
                     */
                    success: function (resp) {
                        if (resp.error) {
                            return false;
                        }

                        validateResult = resp.result;
                    },
                });
                return validateResult;
            }
            , $.mage.__('The order has been existed!')
        );

        validator.addRule(
            'membership-sort-order-with-value',
            function (value) {
                var source = uiRegistry.get('rewardpoints_membership_edit.rewardpoints_membership_edit_data_source');
                var ajaxUrl = source.validate_tier_order_withvalue;

                var data = {
                    'membershipId': source.data.membership.id,
                    'sort_order': source.data.membership.sort_order,
                    'condition_reach_tier': source.data.membership.condition_reach_tier,
                    'condition_reach_tier_value': source.data.membership.condition_reach_tier_value,
                    'form_key': window.FORM_KEY
                };

                let validateResult = true;

                if (data.sort_order && data.condition_reach_tier && data.condition_reach_tier_value) {
                    $.ajax({
                        url: ajaxUrl,
                        data: data,
                        dataType: 'json',
                        type: 'POST',
                        showLoader: true,
                        async: false,

                        /**
                         * Success callback.
                         * @param {Object} resp
                         * @returns {Boolean}
                         */
                        success: function (resp) {
                            if (resp.error) {
                                return false;
                            }
                            if (resp.result) {
                                uiRegistry.get('rewardpoints_membership_edit.areas.membership.membership.condition_reach_tier_value').error('');
                                uiRegistry.get('rewardpoints_membership_edit.areas.membership.membership.condition_reach_tier').error('');
                                uiRegistry.get('rewardpoints_membership_edit.areas.membership.membership.sort_order').error('');
                            }

                            validateResult = resp.result;
                        },
                    });
                }
                return validateResult;
            }
            , $.mage.__('You cannot set "Value of Criteria to Reach Tier" is less than the Tier group whose sort order is lower')
        );

    });