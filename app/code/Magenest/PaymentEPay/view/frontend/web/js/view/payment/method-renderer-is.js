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
                type: 'vnpt_epay_is',
                component: 'Magenest_PaymentEPay/js/view/payment/method-renderer/vnptpayment-is'
            }
        );
        return Component.extend({});
    }
);
