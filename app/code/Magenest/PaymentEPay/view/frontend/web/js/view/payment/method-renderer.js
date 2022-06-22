define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'vnpt_epay',
                component: 'Magenest_PaymentEPay/js/view/payment/method-renderer/vnptpayment'
            }
        );
        return Component.extend({});
    }
);