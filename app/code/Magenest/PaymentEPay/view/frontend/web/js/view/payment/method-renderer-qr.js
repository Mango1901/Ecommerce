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
                type: 'vnpt_epay_qrcode',
                component: 'Magenest_PaymentEPay/js/view/payment/method-renderer/vnptpayment-qr'
            }
        );
        return Component.extend({});
    }
);
