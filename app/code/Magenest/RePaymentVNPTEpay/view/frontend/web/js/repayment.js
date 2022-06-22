define([
    "jquery",'Magento_Customer/js/model/customer','mage/url','mage/translate','Magenest_PaymentEPay/js/jquery-1.11.1.min'
], function($,customer,urlBuilder,$t,jqueryJS){
    "use strict";
    return function repayment(config, element) {
        $("#repayment").click(function () {
            const payMethod = $('input:radio[name="payType"]:checked').val();
            var payOpt = customer.isLoggedIn() ? (payMethod === 'DC' ? 'PAY_WITH_RETURNED_TOKEN' : 'PAY_AND_CREATE_TOKEN') : '';
            var payType = (payMethod === 'DC') ? 'payOptDC' : 'payOptionIC';

            var url = window.location.href;

            var splitUrl = url.split('/');

            var orderId = splitUrl[7];

            var serviceUrl = urlBuilder.build('epay/payment/repayment');
            var servicecallBackUrl = urlBuilder.build('epay/payment/responserepayment');
            var servicereqIPN = urlBuilder.build('epay/payment/ipn');
            $.ajax({
                url : serviceUrl,
                type : 'POST',
                dataType : 'json',
                data : {payOption : payOpt, payType : payType, orderId: orderId},
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
        })
    }
});
