/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/smart-keyboard-handler',
    'mage/mage',
    'mage/ie-class-fixer',
    'domReady!'
], function ($, keyboardHandler) {
    'use strict';

    $('.cart-summary').mage('sticky', {
        container: '#maincontent'
    });

    $('.panel.header > .header.links').clone().appendTo('#store\\.links');
    $('#store\\.links li a').each(function () {
        var id = $(this).attr('id');

        if (id !== undefined) {
            $(this).attr('id', id + '_mobile');
        }
    });

    $(document).on('click', ".footer-mid [data-content-type='heading']",function(){
        $(this).toggleClass('active')  ;
        return false;
    });

    // $(window).scroll(function () {
    //     if ($(this).scrollTop() > 150) {
    //         $('.back-to-top').fadeIn();
    //     } else {
    //         $('.back-to-top').fadeOut();
    //     }
    // });

    // $(document).on('click', ".back-to-top",function(e){
    //     e.preventDefault();
    //     $("html, body").animate({
    //         scrollTop: 0
    //     }, 500);
    //     return false;
    // });

    $(document).on('click', ".menu-collapse",function(){
        $(this).parents('.magemenu-menu').find('.itemMenu').removeClass('active')  ;
        $(this).parent().toggleClass('active')  ;
        return false;
    });

    keyboardHandler.apply();
});
