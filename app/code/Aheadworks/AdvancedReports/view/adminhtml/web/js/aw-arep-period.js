/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

/**
 * Initialization widget to daterange
 *
 * @method _create()
 * @method _setCustomRange()
 * @method _applyPeriod()
 * @method _cancelPeriod()
 * @method _dateToString(d)
 * @method _switchDateRange()
 * @method _override(object, methodName, callback)
 * @method _after(extraBehavior)
 */
define([
    'jquery',
    'timeframe'
], function($) {
    "use strict";

    $.widget('mage.awArepPeriod', {
        options: {
            earliest: '',
            latest: '',
            weekOffset: 0,
            ranges: '',
            periodDateFromSelector: '#awarep-period_date_from',
            periodDateToSelector: '#awarep-period_date_to',
            customDateRangeSelector: '#awarep-custom_date_range',
            applyPeriodButtonSelector: '#awarep-apply-period',
            cancelPeriodButtonSelector: '#awarep-cancel-period',
            calendarsHeaderSelector: '#awarep-calendars_header',
            calendarsContainerSelector: '#awarep-calendars-container'
        },

        /**
         * Initialize widget
         * @private
         */
        _create: function () {
            var self = this;

            this.datePicker = new Timeframe('awarep-calendars', {
                startField: 'awarep-period_date_from',
                endField: 'awarep-period_date_to',
                resetButton: 'reset',
                header: 'awarep-calendars_header',
                form: 'awarep-calendars-container',
                earliest: this.options.earliest,
                latest: this.options.latest,
                weekOffset: this.options.weekOffset
            });
            this.datePicker.parseField('start', true);
            this.datePicker.parseField('end', true);
            this.datePicker.selectstart = true;
            this.datePicker.populate().refreshRange();

            this._override(this.datePicker, 'handleDateClick', this._after(function(element, couldClear) {
                $(self.options.periodDateFromSelector).trigger('input');
            }));

            $(this.options.periodDateFromSelector).on('input', this._setCustomRange.bind(this));
            $(this.options.periodDateToSelector).on('input', this._setCustomRange.bind(this));
            $(this.options.customDateRangeSelector).on('change', this._switchDateRange.bind(this));
            $(this.options.applyPeriodButtonSelector).on('click', this._applyPeriod.bind(this));
            $(this.options.cancelPeriodButtonSelector).on('click', this._cancelPeriod.bind(this));
        },

        /**
         * Set custom range
         * @private
         */
        _setCustomRange: function() {
            $(this.options.customDateRangeSelector).val('custom');
        },

        /**
         * Aplly period click
         * @private
         */
        _applyPeriod:  function() {
            var type = $(this.options.customDateRangeSelector).val(),
                dateFrom = new Date($(this.options.periodDateFromSelector).val()),
                dateTo = new Date($(this.options.periodDateToSelector).val()),
                params = document.location.search.replace('?', '').toQueryParams();

            Object.extend(params, {
                period_type: type,
                period_from: this._dateToString(dateFrom),
                period_to: this._dateToString(dateTo)
            });
            document.location.search = '?' + $.param(params);
        },

        /**
         * Cancel button click
         * @private
         */
        _cancelPeriod:  function() {
            $(this.options.calendarsHeaderSelector).removeClass('opened');
            $(this.options.calendarsContainerSelector).removeClass('is_displayed');
        },

        /**
         * Convert date to string
         * @param d
         * @returns {string}
         * @private
         */
        _dateToString: function(d) {
            return d.getFullYear().toString()
                + '-' + ('0' + (d.getMonth() + 1)).slice(-2)
                + '-' + ('0' + d.getDate()).slice(-2);
        },

        /**
         * Switch date range
         * @private
         */
        _switchDateRange: function() {
            var dateFrom = $(this.options.periodDateFromSelector),
                dateTo = $(this.options.periodDateToSelector),
                periodType = $(this.options.customDateRangeSelector).val();

            if (!Object.isUndefined(this.options.ranges[periodType])) {
                dateFrom.val(this.options.ranges[periodType].from);
                dateTo.val(this.options.ranges[periodType].to);
            }
            this.datePicker.range.set('start', new Date(dateFrom.val()));
            this.datePicker.range.set('end', new Date(dateTo.val()));
            this.datePicker.parseField('start', false);
            this.datePicker.parseField('end', false);
            $(this.options.calendarsHeaderSelector).html(dateFrom.val() + ' - ' + dateTo.val());
        },

        /**
         * For override original method
         * @param object
         * @param methodName
         * @param callback
         * @private
         */
        _override: function(object, methodName, callback) {
            object[methodName] = callback(object[methodName])
        },

        /**
         * Call custom method after original
         * @param extraBehavior
         * @returns {Function}
         * @private
         */
        _after: function(extraBehavior) {
            return function(original) {
                return function() {
                    var returnValue = original.apply(this, arguments)
                    extraBehavior.apply(this, arguments)
                    return returnValue
                }
            }
        }
    });

    return $.mage.awArepPeriod;
});
