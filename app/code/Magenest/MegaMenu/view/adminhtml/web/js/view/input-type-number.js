require([
    'jquery',
    'Magento_Ui/js/lib/view/utils/async'
], function ($) {
    'use strict';
    $.async('input[type="number"]', function () {
        $('input[type="number"]')
            .prop('step', 0.1)
            .on("input", function (event) {
                event.currentTarget.value = event.currentTarget.valueAsNumber;
                // if (!/^[0-9]{1}$/.test(event.key) && event.keyCode !== 8) {
                //     event.preventDefault();
                // }
            });
    });
});