require(["jquery"] , function($) {
    $('body').on('click', '.collapse-list .collapse-title', function () {
        $(this).parent().toggleClass('open');
    });
});