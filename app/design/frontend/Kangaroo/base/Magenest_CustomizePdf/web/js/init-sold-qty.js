define([
    'jquery',
    'mage/url'
], function ($, urlBuilder) {
    'use strict';
    return function(config, element) {
        $(document).ready(function() {
            $('#downloadable-link').click(function () {
                let url = urlBuilder.build('customizepdf/soldqty/update');
                let productId = config.productId
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        productId: productId,
                    },
                    success: function (response) {
                        if (response.success) {
                            $("#sold_qty").text(response.sold_qty);
                        }
                    },
                    error: function (xhr, status, errorThrown) {
                        console.log('Error happens. Try again.');
                    }
                });
            });
        })
    };
});
