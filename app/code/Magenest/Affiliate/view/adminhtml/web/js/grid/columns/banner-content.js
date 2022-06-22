
define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Magenest_Affiliate/grid/cells/content'
        },
        getLabel: function (record) {
            return record['content_html'];
        }
    });
});
