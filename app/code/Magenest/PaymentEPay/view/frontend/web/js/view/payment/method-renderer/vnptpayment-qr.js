define([
        'ko',
        'jquery',
        'Magenest_PaymentEPay/js/jquery-1.11.1.min',
        'underscore',
        'Magento_Checkout/js/view/payment/default',
        'mage/translate',
        'Magento_Checkout/js/model/payment/additional-validators',
        'mage/url',
        'mage/storage',
        'Magento_Checkout/js/action/redirect-on-success',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/set-payment-information',
    ],function (
    ko,
    $,
    jqueryJS,
    _,
    Component,
    $t,
    additionalValidators,
    urlBuilder,
    storage,
    redirectOnSuccessAction,
    customer,
    quote,
    fullScreenLoader,
    setPaymentInformation,
    ){
        'use strict';
        $(document).on('change','input:radio[name="payType"]',function(){
            if ($(this).is(':checked') && $(this).val() === 'IC') {
                $('button.checkout').attr('disabled', false);
            } else if ($(this).is(':checked') && $(this).val() === 'DC') {
                $('button.checkout').attr('disabled', false);
            } else if ($(this).is(':checked') && $(this).val() === 'EW') {
                $('button.checkout').attr('disabled', false);
            } else {
                $('button.checkout').attr('disabled', true);
            }
        });
        window.addEventListener('message', function(e) {
            var serviceUrlClose = urlBuilder.build('epay/payment/returnPayment');
            if (e.data.closeLayer === 'close'){
                window.top.closeLayer();
                $.ajax({
                    url : serviceUrlClose,
                    type : 'POST',
                    dataType : 'json',
                    data : {close : e.data.closeLayer},
                    success : function(res) {
                        window.location.href = res.url;
                    },
                    error : function() {
                        alert($t('There was an error in processing !'));
                    }
                });
            }
        });
        return Component.extend({
            defaults: {
                template: 'Magenest_PaymentEPay/payment/vnptpayment-qr'
            },
            redirectAfterPlaceOrder: false,
            getCode: function () {
                return 'vnpt_epay_qrcode';
            },
            initObservable: function () {
                this._super();
                return this;
            },
            placeOrder: function (data, event) {
                var self = this;
                if (event) {
                    event.preventDefault();
                }
                if (this.validate() && additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);

                    this.getPlaceOrderDeferredObject().done( function () {
                            self.afterPlaceOrder();
                            const payMethod = $('#payTypeQR').val();
                                var payOpt = customer.isLoggedIn() ? (payMethod === 'VA' ? 'PAY_WITH_RETURNED_TOKEN' : 'PAY_AND_CREATE_TOKEN') : '';
                                var payType = (payMethod === 'VA') ? 'VA' : '';

                            var serviceUrl = urlBuilder.build('epay/payment/checkout');
                                $.ajax({
                                    url : serviceUrl,
                                    type : 'POST',
                                    dataType : 'json',
                                    data : {payOption : payOpt, payType : payType},
                                    showLoader: true,
                                    success : function(res) {
                                        if (res.resultMsg === "SUCCESS") {
                                            if(res.notification){
                                                alert($t(res.notification));
                                                var serviceUrlClose = urlBuilder.build('epay/payment/returnPayment');
                                                $.ajax({
                                                    url : serviceUrlClose,
                                                    type : 'POST',
                                                    dataType : 'json',
                                                    data : {close : res.notification},
                                                    success : function(res) {
                                                        window.location.href = res.url;
                                                    },
                                                    error : function() {
                                                        alert($t('There was an error in processing !'));
                                                    }
                                                });
                                            } else{
                                                var result = '';
                                                if (typeof res.qrCode != 'undefined') {
                                                    result = "<img src='data:image/png;base64," + res.qrCode + "'" + '>';
                                                }
                                                $('#payment-qr-code-renderer').append(result);
                                                $("#payment-qr-code-warning-message").css("display","");
                                                $("#payment-qr-code-message-countdown").css("display","");
                                                var timeleft = 300;
                                                setInterval(function () {
                                                    if (timeleft == 0) {
                                                        alert($t("QR code had been expired"));
                                                        window.location.reload();
                                                    }
                                                    document.getElementById("payment-qr-code-countdown").innerHTML = timeleft;
                                                    timeleft -= 1;
                                                }, 1000);
                                            }
                                        } else {
                                            alert($t(res.resultMsg));
                                        }
                                    },
                                    error : function() {
                                        alert($t('There was an error in processing !'));
                                    }
                                });
                        }
                    ).always(
                        function () {
                            self.isPlaceOrderActionAllowed(true);
                        }
                    );
                    return true;
                }
                return false;
            },
            getCcAvailableTypes: function() {
                return window.checkoutConfig.payment.vnpt_epay_qrcode.availableTypes['vnpt_epay_qrcode'];
            },
        });
    }
);
