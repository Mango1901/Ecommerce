require(["jquery"] , function($) {
    $('body').on('click', '.category-item .title', function () {
        $(this).parent().toggleClass('open');
    });
});