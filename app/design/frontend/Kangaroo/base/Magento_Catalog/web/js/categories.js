define([
    'jquery',
    'matchMedia',
    'slick'
], function($, mediaCheck){
    let categoriesItemLv2 = $('.level-2 .sub-categories-item');
    let categorieslistLv2 = $('.level-2 .sub-categories-list');
    let categoriesItem = $('body:not(.page-layout-category_brand) .sub-categories:not(.level-2) .sub-categories-item');
    let categorieslist = $('body:not(.page-layout-category_brand) .sub-categories:not(.level-2) .sub-categories-list');
    let subCategoryBrandItem = $('.page-layout-category_brand .sub-categories .sub-categories-item');
    let subCategoryBrandList = $('.page-layout-category_brand .sub-categories .sub-categories-list');
    let categoryBrandServiceItem = $('.page-layout-category_brand .category-description .pagebuilder-column');
    let categoryBrandServiceList = $('.page-layout-category_brand .category-description .pagebuilder-column-group');

    if (categoriesItemLv2.length > 7) {
        categorieslistLv2.not('.slick-initialized').slick({
            arrows: false,
            autoplay: false,
            dots: false,
            slidesToShow: 7,
            slidesToScroll: 7,

            responsive: [
                {
                    breakpoint: 1023,
                    settings: {
                        slidesToShow: 6,
                        slidesToScroll: 6,
                    }
                },
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 5,
                        slidesToScroll: 5,
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        arrows: true,
                        slidesToShow: 4,
                        slidesToScroll: 4,
                    }
                },
                {
                    breakpoint: 574,
                    settings: {
                        arrows: true,
                        slidesToShow: 3,
                        slidesToScroll: 3,
                    }
                }
            ]
        });
    }

    mediaCheck({
        media: '(max-width: 1023px)',
        // Switch to Mobile, Tablet Version
        entry: function () {
            categorieslist.not('.slick-initialized').slick({
                arrows: false,
                autoplay: false,
                dots: false,
                slidesToShow: 4,
                slidesToScroll: 4,
                responsive: [
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3,
                        }
                    },
                    {
                        breakpoint: 767,
                        settings: {
                            rows: 2,
                            arrows: true,
                            slidesToShow: 2,
                            slidesToScroll: 2,
                        }
                    }
                ]
            });
        },
        // Switch to Desktop Version
        exit: function () {
            if (categoriesItem.length > 5) {
                categorieslist.not('.slick-initialized').slick({
                    arrows: false,
                    autoplay: false,
                    dots: false,
                    slidesToShow: 5,
                    slidesToScroll: 5,
                    responsive: [
                        {
                            breakpoint: 767 ,
                            settings: {
                                rows: 2,
                                arrows: true,
                                slidesToShow: 2,
                                slidesToScroll: 2,
                            }
                        }
                    ]
                });
            }
        }
    });

    var settingsDesk = {
            slidesToShow: 5,
            slidesToScroll: 5,
            arrows: false,
        },
        settingsTablet = {
            slidesToShow: 3,
            slidesToScroll: 3,
            arrows: false,
        },
        settingsMobile = {
            slidesToShow: 2,
            slidesToScroll: 2,
            arrow: true,
        };

    if (subCategoryBrandItem.length <= 4) {
        settingsDesk = "unslick";
        settingsTablet = "unslick";
        settingsMobile = "unslick";
    } else if (subCategoryBrandItem.length <= 6) {
        settingsDesk = "unslick";
        settingsTablet = "unslick";
    } else if (subCategoryBrandItem.length <= 10) {
        settingsDesk = "unslick";
    }

    subCategoryBrandList.not('.slick-initialized').slick({
        rows: 2,
        autoplay: false,
        dots: false,
        slidesToShow: 5,
        slidesToScroll: 5,
        responsive: [
            {
                breakpoint: 9999,
                settings: settingsDesk
            },
            {
                breakpoint: 1023,
                settings: settingsTablet
            },
            {
                breakpoint: 767,
                settings: settingsMobile
            }
        ]
    });

    mediaCheck({
        media: '(max-width: 1023px)',
        // Switch to Mobile, Tablet Version
        entry: function () {
            categoryBrandServiceList.not('.slick-initialized').slick({
                arrows: false,
                autoplay: false,
                dots: false,
                slidesToShow: 4,
                slidesToScroll: 4,
                responsive: [
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3,
                        }
                    },
                    {
                        breakpoint: 767,
                        settings: {
                            arrows: true,
                            slidesToShow: 2,
                            slidesToScroll: 2,
                        }
                    }
                ]
            });
        },
        // Switch to Desktop Version
        exit: function () {
            if (categoryBrandServiceItem.length > 5) {
                categoryBrandServiceList.not('.slick-initialized').slick({
                    arrows: false,
                    autoplay: false,
                    dots: false,
                    slidesToShow: 5,
                    slidesToScroll: 5,
                    responsive: [
                        {
                            breakpoint: 992,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 3,
                            }
                        },
                        {
                            breakpoint: 767 ,
                            settings: {
                                arrows: true,
                                slidesToShow: 2,
                                slidesToScroll: 2,
                            }
                        }
                    ]
                });
            }
        }
    });
});
