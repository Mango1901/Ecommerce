define([
    'jquery',
    'mage/translate',
], function ($, $t) {
    'use strict';
    let passValidate = true;

    return function (config) {
        $(config.elementValue).on('change keyup paste', function () {
            const val = $(this).val();
            if (!val.match(/^-?\d+$/)) {
                passValidate = false;
                $(config.elementMessage).text($t('Value commission for affiliate must be number'));
            } else {
                const valueInFloat = Number.parseFloat(val);
                if (valueInFloat > config.value || valueInFloat < 0) {
                    passValidate = false;
                    $(config.elementMessage).text($t('Value commission for affiliate must greater than 0 and less than or equal to %1').replace('%1', config.value));
                } else {
                    passValidate = true;
                    const remainValue = config.value - valueInFloat;
                    $(config.elementMessage).text('');
                    $(config.elementCustomerValue).val(remainValue)
                    $(config.elementTextCustomerValue).text(remainValue)
                }
            }
        });

        $(config.elementValueSecond).on('change keyup paste', function () {
            const val = $(this).val();
            if (!val.match(/^-?\d+$/)) {
                passValidate = false;
                $(config.elementMessage).text($t('Value second commission for affiliate must be number'));
            } else {
                const valueInFloat = Number.parseFloat(val);
                if (valueInFloat > config.valueSecond || valueInFloat < 0) {
                    passValidate = false;
                    $(config.elementMessage).text($t('Value second commission for affiliate must greater than 0 and less than or equal to %1').replace('%1', config.valueSecond));
                } else {
                    passValidate = true;
                    const remainValue = config.value - valueInFloat;
                    $(config.elementMessage).text('');
                    $(config.elementCustomerValueSecond).val(remainValue)
                    $(config.elementTextCustomerValueSecond).text(remainValue)
                }
            }
        });

        $(config.elementButton).click(function () {
            if (!passValidate) {
                $(config.elementMessage).text($t('Please validate input before submit'));
            } else {
                $(config.elementForm).submit();
            }
        })
    }
});
