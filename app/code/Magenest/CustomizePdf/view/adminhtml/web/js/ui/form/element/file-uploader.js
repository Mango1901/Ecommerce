define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/file-uploader'
], function ($, _, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            elementToDisable: ''
        },
        lastenter: '',

        /**
         * Retrieve input name
         *
         * @param {String} inputName
         * @param {String} index
         * @return {String}
         */
        getInputName: function (inputName, index) {
            return this.inputName + '[' + index + '][' + inputName + ']';
        }
    });
});
