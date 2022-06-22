/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_AdvancedReports/ui/grid/cells/url'
        },
        getRowLabel: function(row) {
            return (typeof(row['row_label_' + this.index]) != 'undefined')
                ? row['row_label_' + this.index]
                : row['row_label'];
        },
        getRowUrl: function(row) {
            return (typeof(row['row_url_' + this.index]) != 'undefined')
                ? row['row_url_' + this.index]
                : row['row_url'];
        }
    });
});
