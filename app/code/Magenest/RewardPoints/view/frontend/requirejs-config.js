var config = {
    'config':{
        'mixins': {
            'Magento_SalesRule/js/view/payment/discount': {
                'Magenest_RewardPoints/js/view/payment/discount':true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'Magenest_RewardPoints/js/view/product/variation-point': true
            }
        }
    }
};