define([
    'Magento_Ui/js/grid/columns/column',
    'jquery',
    'jsonViewer',
    'mage/template',
    'text!Magenest_AdminActivity/templates/grid/cells/json-viewer.html',
    'underscore',
    'Magento_Ui/js/modal/modal'
], function (Column, $, jsonViewer, mageTemplate, jsonViewerTemplate, _) {
    "use strict";

    return Column.extend({
        defaults: {},

        /**
         * Ment to preprocess data associated with a current columns' field.
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {String}
         */
        getLabel: function (row) {
            return row[this.index].substring(0, 100) + '...(' + $.mage.__('See more') + ')';
        },

        /**
         * @param {Object} row
         */
        unserialize: function (row) {
            var modalHtml = mageTemplate(
                jsonViewerTemplate, {
                    json: row[this.index]
                }),
                previewPopup = $('<div/>').jsonViewer(JSON.parse(row[this.index]), {withQuotes: true});
            previewPopup.html(modalHtml + previewPopup.html());
            previewPopup.modal({
                title: this.getTitle(row),
                innerScroll: true,
                clickableOverlay: true,
                buttons: []
            }).trigger('openModal');
        },

        /**
         * @param {Object} row
         * @returns {Boolean}
         */
        isJsonSerializer: function () {
            return this['json_serializer'] || false;
        },

        /**
         * @param {Object} row
         * @returns {String}
         */
        getTitle: function (row) {
            return _.escape(row['job']);
        },

        /**
         * @param {Object} row
         * @returns {Function}
         */
        getFieldHandler: function (row) {
            if (this.isJsonSerializer()) {
                return this.unserialize.bind(this, row);
            }
        }
    });
});
