require([
    'jquery',
    'underscore',
    'Magento_Customer/js/customer-data'
], function ($, _, customerData) {
    'use strict';

    $(document).ready(function () {
        var wishList = customerData.get('wishlist');
        processWishListItems(wishList().items);
        wishList.subscribe(function (value) {
            processWishListItems(value.items);
        })
    });
    $(document).on('customer-data-reload', function (event, sections) {
        if ((_.isEmpty(sections) || _.contains(sections, 'wishlist'))) {
            const wishList = customerData.get('wishlist')();
            processWishListItems(wishList.items)
        }
    });

    function processWishListItems(items) {
        if (typeof items !== "undefined") {
            $.each(items, function (index, item) {
                $('.towishlist[data-product-id='+ item.product_id +']').addClass('active');
            });
        }
    }
});
