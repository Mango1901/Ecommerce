define(['jquery'], function ($) {
    'use strict';
    return function (targetWidget) {
        $.widget('mage.productListToolbarForm', targetWidget, {
            /**
             * @param {jQuery.Event} event
             * @private
             */
            _processLink: function (event) {
                this.changeUrl(
                    event.data.paramName,
                    $(event.currentTarget).data('value'),
                    event.data.default
                );
            },
        });

        return $.mage.productListToolbarForm;
    };
});
