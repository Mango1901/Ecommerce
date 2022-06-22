/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/shipping-save-processor/payload-extender': {
                'Magenest_OrderExtraInformation/js/model/shipping-save-processor/payload-extender-mixin': true
            }
        }
    }
};
