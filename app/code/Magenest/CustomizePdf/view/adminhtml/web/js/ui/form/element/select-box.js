define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/abstract'
], function ($, _, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            elementToDisable: ''
        },
        lastenter: '',

        initObservable: function () {
            this._super();

            return this;
        }
    });
});
