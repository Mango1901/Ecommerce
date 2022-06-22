require(['jquery', 'Magento_Customer/js/customer-data'], function ($, customerData) {
    $('.product-copy-share').on('click', function () {
        var affiliateParam = customerData.get('affiliate')();
        var url = window.location.href;
        if (affiliateParam.param) {
            url = url + affiliateParam.param;
        }
        navigator.clipboard.writeText(url);
        $(this).find('.product-copy-link').addClass('copied');
    });

    customerData.get('affiliate').subscribe(function (value) {
        if (value.param) {
            window.shareLink = window.shareLink + value.param;
        }
    });

    $(document).ready(function () {
        var affiliateParam = customerData.get('affiliate')();
        if (affiliateParam.param) {
            window.shareLink = window.shareLink + affiliateParam.param;
        }
    });
});
