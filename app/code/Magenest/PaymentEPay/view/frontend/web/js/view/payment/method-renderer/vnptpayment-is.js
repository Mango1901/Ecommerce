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
                template: 'Magenest_PaymentEPay/payment/vnptpayment-is'
            },
            redirectAfterPlaceOrder: false,
            getCode: function () {
                return 'vnpt_epay_is';
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
                            const payMethod = $("#payTypeIs").val();
                            if(payMethod == "IS"){
                                var payOpt = customer.isLoggedIn() ? (payMethod === 'DC' ? 'PAY_WITH_RETURNED_TOKEN' : 'PAY_AND_CREATE_TOKEN') : '';
                                var payType = "IS";
                            } else {
                                payOpt = customer.isLoggedIn() ? (payMethod === 'DC' ? 'PAY_WITH_RETURNED_TOKEN' : 'PAY_AND_CREATE_TOKEN') : '';
                                payType = (payMethod === 'DC') ? 'payOptDC' : 'payOptionIC';
                            }

                            var serviceUrl = urlBuilder.build('epay/payment/checkout');
                            var servicecallBackUrl = urlBuilder.build('epay/payment/responseIS');
                            var servicereqIPN = urlBuilder.build('epay/payment/ipn');
                            $.ajax({
                                url : serviceUrl,
                                type : 'POST',
                                dataType : 'json',
                                data : {payOption : payOpt, payType : payType},
                                success : function(res) {
                                    if (res.success) {
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
                                        }else{
                                            if (typeof document.getElementById("megapayForm") != 'undefined') {
                                                document.getElementById('megapayForm').setAttribute("id", "megapayForm2")
                                            }
                                            if (typeof document.querySelector(".payment-method._active div.payment-method-content form.megapayForm") != 'undefined') {
                                                document.querySelector(".payment-method._active div.payment-method-content form.megapayForm").setAttribute("id", "megapayForm");
                                            }
                                            var domain = res.domain;
                                            var servicereqDomain = res.reqDomain;
                                            document.getElementById('megapayForm').elements["description"].value = res.description;
                                            document.getElementById('megapayForm').elements["amount"].value = res.amount;
                                            document.getElementById('megapayForm').elements["timeStamp"].value = res.timeStamp;
                                            document.getElementById('megapayForm').elements['invoiceNo'].value = res.invoiceNo;
                                            document.getElementById('megapayForm').elements['goodsNm'].value = res.goodsNm;
                                            document.getElementById('megapayForm').elements['callBackUrl'].value = servicecallBackUrl;
                                            document.getElementById('megapayForm').elements['notiUrl'].value = servicereqIPN;
                                            document.getElementById('megapayForm').elements['reqDomain'].value = servicereqDomain;
                                            document.getElementById('megapayForm').elements['merTrxId'].value = res.merTrxId;
                                            document.getElementById('megapayForm').elements['merId'].value = res.merId;
                                            document.getElementById('megapayForm').elements["merchantToken"].value = res.token;
                                            document.getElementById('megapayForm').elements["buyerLastNm"].value = res.lastname;
                                            document.getElementById('megapayForm').elements["buyerFirstNm"].value = res.customerFirstName;
                                            document.getElementById('megapayForm').elements["userId"].value = res.userId;
                                            document.getElementById('megapayForm').elements["buyerEmail"].value = res.email;
                                            document.getElementById('megapayForm').elements["bankCode"].value = res.bankCode;
                                            document.getElementById('megapayForm').elements["termIs"].value = res.termIs;
                                            if (res.payToken) {
                                                document.getElementById('megapayForm').elements["payToken"].value = res.payToken;
                                            }
                                            openPayment(1, domain);
                                        }
                                    } else {
                                        alert($t(res.mes));
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
                return window.checkoutConfig.payment.vnpt_epay_is.availableTypes['vnpt_epay_is'];
            },
        });
    }
);
