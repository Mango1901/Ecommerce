/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            owlCarousel: "Magenest_Slider/js/owl.carousel",
            owlCarouselExtend: "Magenest_Slider/js/owl.carousel.extend",
        }
    },
    shim: {
        "Magenest_Slider/js/owl.carousel": ["jquery"],
        "Magenest_Slider/js/owl.carousel.extend": ["jquery", "owlCarousel"]
    }
};
