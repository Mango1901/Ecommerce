/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'underscore',
    'uiCollection'
], function (_, Collection) {
    'use strict';

    return Collection.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReports/ui/grid/totals',
            columnsProvider: 'ns = ${ $.ns }, componentType = columns',
            imports: {
                addColumns: '${ $.columnsProvider }:elems',
                totals: '${ $.provider }:data.totals',
            },
        },

        /**
         * Initializes observable properties.
         *
         * @returns {Totals} Chainable
         */
        initObservable: function () {
            this._super()
                .track({
                    totals: []
                });

            return this;
        },

        /**
         * Adds columns whose visibility can be controlled to the component
         *
         * @param {Array} columns - Elements array that will be added to component
         * @returns {Columns} Chainable
         */
        addColumns: function (columns) {
            columns = _.where(columns, {
                topTotalsVisible: true
            });

            this.insertChild(columns);

            return this;
        },
    });
});
