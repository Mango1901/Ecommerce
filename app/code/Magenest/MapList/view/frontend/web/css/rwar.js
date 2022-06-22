jQuery(".gm-style-iw").find('.gm-iw').each(function(i,element) {
    jQuery(element).closest('.gm-style-iw').parent().css('display' , 'none');

});