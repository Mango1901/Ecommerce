require(["jquery"] , function($) {
    $(".qty-minus").click(function() {
        let minus = $(this).parents('.input-group').find('input').val();
        if (minus > 0) {
            $(this).parents('.input-group').find('input').val(minus - 1);
        } else {
            $(this).parents('.input-group').find('input').val(0);
        }
    });
    $(".qty-plus").on("click", function() {
        let plus = Number($(this).parents('.input-group').find('input').val());
        $(this).parents('.input-group').find('input').val(plus + 1);
    });
});
