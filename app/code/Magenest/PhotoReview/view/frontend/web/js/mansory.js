define([
    'jquery',
    'Magenest_PhotoReview/js/lib/masonry-pkgd',
    'Magenest_PhotoReview/js/lib/imagesloaded-pkgd',
    'mage/storage',
    'underscore'
], function ($, Masonry, ImagesLoaded, storage, _) {
    'use strict';

    return function (config, element) {
        $(element).hide();
        var $mansory = $(element).imagesLoaded(function () {
            $(element).fadeIn();
            $mansory = new Masonry(element, {
                itemSelector: ".widget-flex-grid-item",
                columnWidth: ".photoreview-items",
                percentPosition: false,
                gutter: 15
            });
            // bind event
            $mansory.once('layoutComplete', function () {
                $('.toolbar-bottom').attr('style','display: block')
            });
            // // trigger initial layout
            $mansory.layout();
        });
    }
});
