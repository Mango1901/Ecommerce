define([
    "jquery",
    'domReady!',
    "Magenest_Slider/js/owl.carousel"
], function($) {
    'use strict';

    function sliderSyncing(sync1, sync2, optionSync1, optionSync2) {
        var syncedSecondary = true;

        /* optionSync1, optionSync2 = [items, margin, nav, dots, animateIn, animateOut, autoHeight, autoplay, autoplayTimeout, autoplayHoverPause, mouseDrag, items-0, items-480, items-768, items-1024 ] */
        /*
        * optionSync1.autoplay === optionSync2.autoplay
        * optionSync1.autoplayTimeout === optionSync2.autoplayTimeout
        * optionSync1.autoplayHoverPause === optionSync2.autoplayHoverPause
        * */

        sync1.owlCarousel({
            items: optionSync1[0],
            margin: optionSync1[1],
            nav: optionSync1[2],
            dots: optionSync1[3],
            animateIn: optionSync1[4],
            animateOut: optionSync1[5],
            autoHeight: optionSync1[6],
            autoplay: optionSync1[7],
            autoplayTimeout: optionSync1[8],
            autoplayHoverPause: optionSync1[9],
            mouseDrag: optionSync1[10],
            loop: true,
            center: true,
            responsive:{
                0:{
                    items: optionSync1[11]
                },
                480:{
                    items: optionSync1[12]
                },
                768:{
                    items: optionSync1[13]
                },
                1024:{
                    items: optionSync1[14]
                }
            }
        }).on('changed.owl.carousel', syncPosition);

        sync2
            .on('initialized.owl.carousel', function () {
                sync2.find(".owl-item").eq(0).addClass("current");
            })
            .owlCarousel({
                items: optionSync2[0],
                margin: optionSync2[1],
                nav: optionSync2[2],
                dots: optionSync2[3],
                animateIn: optionSync2[4],
                animateOut: optionSync2[5],
                autoHeight: optionSync2[6],
                mouseDrag:  optionSync2[10],
                autoplay: false,
                loop: false,
                center: false,
                responsive:{
                    0:{
                        items: optionSync2[11]
                    },
                    480:{
                        items: optionSync2[12]
                    },
                    768:{
                        items: optionSync2[13]
                    },
                    1024:{
                        items: optionSync2[14]
                    }
                }
            }).on('changed.owl.carousel', syncPosition2);

        function syncPosition(el) {
            //if you set loop to false, you have to restore this next line
            //var current = el.item.index;

            //if you disable loop you have to comment this block
            var count = el.item.count - 1;
            var current = Math.round(el.item.index - (el.item.count / 2) - .5);

            if (current < 0) {
                current = count;
            }
            if (current > count) {
                current = 0;
            }

            //end block

            sync2
                .find(".owl-item")
                .removeClass("current")
                .eq(current)
                .addClass("current");
            var onscreen = sync2.find('.owl-item.active').length - 1;
            var start = sync2.find('.owl-item.active').first().index();
            var end = sync2.find('.owl-item.active').last().index();

            if (current > end) {
                sync2.data('owl.carousel').to(current, 100, true);
            }
            if (current < start) {
                sync2.data('owl.carousel').to(current - onscreen, 100, true);
            }
        }

        function syncPosition2(el) {
            if (syncedSecondary) {
                var number = el.item.index;
                sync1.data('owl.carousel').to(number, 100, true);
            }
        }

        sync2.on("click", ".owl-item", function (e) {
            e.preventDefault();
            var number = $(this).index();
            sync1.data('owl.carousel').to(number, 300, true);
        });

    }
    return {
        sliderSyncing: sliderSyncing
    };
});