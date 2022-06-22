/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'jquery',
    'underscore',
    'Magento_Ui/js/grid/listing'
], function (ko, $, _, Listing) {
    'use strict';

    return Listing.extend({
        defaults: {
            additionalClasses: '',
            filteredRows: {},
            limit: 5,
            listens: {
                elems: 'filterRowsFromCache',
                '${ $.provider }:data.items': 'filterRowsFromServer'
            },
            productRecentData: {},
            recentProductDataDataCache: []
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();
            this.filteredRows = ko.observable();
            this.initProductsLimit();
            this.hideLoader();
        },

        /**
         * Initialize product limit
         * Product limit can be configured through Ui component.
         * Product limit are present in widget form
         *
         * @returns {exports}
         */
        initProductsLimit: function () {
            if (this.source['page_size']) {
                this.limit = this.source['page_size'];
            }

            return this;
        },

        /**
         * Initializes observable properties.
         *
         * @returns {Listing} Chainable.
         */
        initObservable: function () {
            this._super()
                .track({
                    rows: []
                });

            return this;
        },

        /**
         * Sort and filter rows, that are already in magento storage cache
         *
         * @return void
         */
        filterRowsFromCache: function () {
            this._filterRows(this.rows);
        },

        /**
         * Sort and filter rows, that are come from backend
         *
         * @param {Object} rows
         */
        filterRowsFromServer: function (rows) {
            this._filterRows(rows);
        },

        /**
         * Filter rows by limit and sort them
         *
         * @param {Array} rows
         * @private
         */
        _filterRows: function (rows) {
            this.filteredRows(_.sortBy(rows, 'added_at').reverse().slice(0, this.limit));
            this.reloadRecentProductData(this.filteredRows);
        },

        /**
         * Can retrieve product url
         *
         * @param {Object} row
         * @returns {String}
         */
        getUrl: function (row) {
            return row.url;
        },

        /**
         * Get product attribute by code.
         *
         * @param {String} code
         * @return {Object}
         */
        getComponentByCode: function (code) {
            var elems = this.elems() ? this.elems() : ko.getObservable(this, 'elems'),
                component;

            component = _.filter(elems, function (elem) {
                return elem.index === code;
            }, this).pop();

            return component;
        },

        reloadRecentProductData: function (row) {
            var self = this;
            if (row().length) {
                var ids = [];
                var idsMapping = {};
                $.each(row(), function (index, product) {
                    ids.push(product.id);
                    idsMapping[product.id] = product;
                });
                ids.sort();
                var key = ids.join('-');

                if (!self.recentProductDataDataCache.includes(key)) {
                    self.recentProductDataDataCache.push(key);
                    $.ajax({
                        url: self.reloadRecentProductDataUrl,
                        global: false,
                        data: {
                            ids: ids
                        }
                    }).done(function (response) {
                        if (response.success) {
                            var result = [];
                            $.each(response.data, function (id, value) {
                                $('#product-sold-' + id + ' .sold_qty').html(value.qty);
                                idsMapping[id]['sold_qty'] = value.qty;
                                idsMapping[id]['reviews_summary'] = value.reviews_summary;
                                result.push(idsMapping[id]);
                            });
                            row(result);
                        }
                    });
                }
            }
        }
    });
});
