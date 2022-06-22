/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'underscore',
        'Magento_Ui/js/dynamic-rows/dynamic-rows',
        'rjsResolver',
        'uiRegistry',
        'ko'
    ],
    function ($, _, DynamicRows, resolver, registry, ko) {
        'use strict';

        return DynamicRows.extend({
            defaults: {
                links: {
                    recordData: []
                },
                htmlId: ''
            },
            countIndex: 0,

            /**
             * Initialize observable properties
             */
            initObservable: function () {
                this._super();
                var self = this;

                this.relatedData = this.value;

                _.each(this.relatedData, function (record, index) {
                    self.recordData.push(record);
                });

                $('#edit_form').on('beforeSubmit', function (event) {
                    var isValid = true;

                    _.each(registry.get(self.name).elems(), function (record) {
                        _.any(record.elems(), function (field) {
                            isValid = (isValid && field.validate().valid);

                            if (field.value() == field['isBreak']) {
                                return true;
                            }
                        });
                    });

                    if (!isValid)
                        event.preventDefault();
                });

                return this;
            },

            /**
             * Delete record;
             * @param index
             * @param recordId
             */
            deleteRecord: function (index, recordId) {
                this._super();

                var path = 'dynamic-rows.' + this.htmlId + '-container',
                    data = _.filter(this.source.get(path), function(item) {
                        return item['record_id'] !== recordId
                    });


                this.source.set(
                    'dynamic-rows.' + this.htmlId + '-container',
                    $.extend(true, {}, data)
                );
            },

            /**
             * Set initial property to records data
             *
             * @returns {Object} Chainable.
             */
            setInitialProperty: function () {
                return this;
            },

            /**
             * Add child components
             *
             * @param {Object} data - component data
             * @param {Number} index - record(row) index
             * @param {Number|String} prop - custom identify property
             *
             * @returns {Object} Chainable.
             */
            addChild: function (data, index, prop) {
                index = this.countIndex;
                this.countIndex++;

                return this._super(data, index, prop);
            },

            /**
             * Destroy all dynamic-rows elems
             *
             * @returns {Object} Chainable.
             */
            clear: function () {
                this.countIndex = 0;

                return this._super();
            }
        });
    }
);
