define([], function () {
    'use strict';

    return function (targetModule) {
        targetModule.defaults.templates.filters.numberRange = {
            component: 'Magento_Ui/js/grid/filters/range',
            rangeType: 'number'
        };

        return targetModule;
    };
});
