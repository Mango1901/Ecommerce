/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Ui/js/lib/validation/validator': {
                'Magenest_Directory/js/validator/vn-telephone': true
            },
            'mage/validation': {
                'Magenest_Directory/js/validator/jquery/vn-telephone': true
            }
        }
    },
    'map': {
        '*': {
            'directoryFieldUpdater': 'Magenest_Directory/js/field-updater'
        }
    }
};
