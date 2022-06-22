/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'jquery',
    'underscore',
    'uiCollection',
    'mage/translate',
    'googleapi'
], function ($, _, Collection, $t) {
    'use strict';

    return Collection.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReports/ui/grid/chart',
            columnsProvider: 'ns = ${ $.ns }, componentType = columns',
            imports: {
                addColumns: '${ $.columnsProvider }:elems',
                rows: '${ $.provider }:data.chart.rows',
            },
            listens: {
                elems: 'drawChart',
                rows: 'drawChart',
            },
            chartData: '',
            chart: '',
            chartContainerId: 'aw-arep__data_grid-chart'
        },

        /**
         * Initializes Listing component
         *
         * @returns {Chart} Chainable
         */
        initialize: function () {
            _.bindAll(this, 'drawChart');

            this._super();

            google.charts.load('current', {'packages': ['corechart']});
            $(window).on('resize', this.drawChart);
            return this;
        },

        /**
         * Adds columns whose visibility can be controlled to the component
         *
         * @param {Array} columns - Elements array that will be added to component
         * @returns {Columns} Chainable
         */
        addColumns: function (columns) {
            var self = this;

            columns = _.where(columns, {
                visibleOnChart: true
            });
            this.insertChild(columns);

            this.elems().forEach(function (column) {
                column.on('visible', self.drawChart);
            });
            return this;
        },

        /**
         * Is draw chart
         *
         * @returns {Boolean}
         */
        isDrawChart: function () {
            if ((this.rows && this.rows.length > 1) && (this.elems() && this.elems().length > 1)) {
                return true;
            }
            return false;
        },

        /**
         * Initialization of chart
         *
         * @returns {Void}
         */
        initChart: function () {
            var self = this,
                chartType = this.source.chartType ? this.source.chartType : 'LineChart';

            this.chartData = new google.visualization.DataTable();
            this.chart = new google.visualization.ChartWrapper({
                chartType: chartType,
                containerId: this.chartContainerId,
                dataTable: this.chartData,
                options: this.getChartOptions(chartType)
            });

            google.visualization.events.addListener(this.chart, 'select', function () {
                self.clickOnChartSeries(self.chart.getChart().getSelection());
            });
        },

        /**
         * Retrieve chart options
         *
         * @param {String} chartType - Сhart type for which you want to receive options
         * @returns {Object}
         */
        getChartOptions: function (chartType) {
            var chartOptions = {
                height: 400,
                pointSize: 10,
                lineWidth: 3,
                vAxis:{
                    minValue: 0, maxValue: 5, gridlines: {count: 9},
                    textStyle: {
                        fontSize: 12
                    }
                },
                hAxis: {
                    textStyle: {
                        fontSize: 11
                    }
                },
                tooltip: {
                    textStyle: {
                        fontSize: 12
                    }
                },
                legend: {
                    textStyle: {
                        fontSize: 13
                    }
                },
                colors: this.getColumnColors()
            };

            switch (chartType) {
                case 'ColumnChart':
                    chartOptions.title = $t('Top 10 Products');
                    break;
            }

            return chartOptions;
        },

        /**
         * Draw chart
         *
         * @returns {Void}
         */
        drawChart: function () {
            var yPos = window.scrollY,
                xPos = window.scrollX;

            $('#' + this.chartContainerId).html('');
            if (!this.isDrawChart()) {
                return;
            }

            var self = this,
                chartView = {};

            this.initChart();
            this.elems().each(function (column, index) {
                // Visible/hide serie in legend
                column.googleSerie = {};
                if (index > 0) {
                    self.elems()[index - 1].googleSerie.visibleInLegend = column.visible;
                }
                if ((!column.displayOnChartAfterLoad || (column.displayOnChartAfterLoad && !column.visible))
                    && index > 0
                ) {
                    // Hide column
                    column.googleColumn = ({
                        label: column.label,
                        type: column.chartType,
                        calc: function () {
                            return null;
                        }
                    });
                    // Coloring serie to gray and visible/hide in legend
                    self.elems()[index - 1].googleSerie.color = '#cccccc';
                } else {
                    column.googleColumn = index;
                }
                self.chartData.addColumn({'label': column.label, 'type': column.chartType});
            });

            chartView.columns = this.getColumnsForGoogleChart();
            this.chart.setOption('series', this.getSeriesForGoogleChart());
            this.chartData.addRows(this.getChartRows());
            this.chart.setView(chartView);
            this.chart.draw();
            window.scrollTo(xPos, yPos);
        },

        /**
         * Retrieve culumns for google chart
         *
         * @returns {Array}
         */
        getColumnsForGoogleChart: function() {
            var columns = [];

            this.elems().forEach(function (column) {
                columns.push(column.googleColumn);
            });
            return columns;
        },

        /**
         * Retrieve series for google chart
         *
         * @returns {Array}
         */
        getSeriesForGoogleChart: function() {
            var series = [];

            this.elems().forEach(function (column) {
                series.push(column.googleSerie);
            });
            return series;
        },

        /**
         * Retrieve culumn colors
         *
         * @returns {Array}
         */
        getColumnColors: function() {
            var colors = [];

            this.elems().each(function (column, index) {
                if (index > 0) {
                    colors.push(column.color);
                }
            });
            return colors;
        },

        /**
         * Retrieve chart rows
         *
         * @returns {Array}
         */
        getChartRows: function() {
            var self = this,
                chartRows = [],
                newRow = [];

            this.rows.forEach(function (row) {
                newRow = [];
                self.elems().forEach(function (column) {
                    if (column.chartType == 'number') {
                        newRow.push({'v': parseFloat(row[column.index]), 'f': column.getLabel(row)});
                    } else {
                        newRow.push(column.getLabel(row));
                    }
                });
                chartRows.push(newRow);
            });
            return chartRows;
        },

        /**
         * Click on chart series
         *
         * @private
         * @returns {void}
         */
        clickOnChartSeries: function(sel) {
            if (sel.length > 0) {
                // If row is null, then clicked on the legend
                if (sel[0].row === null) {
                    var index = sel[0].column,
                        chartView = this.chart.getView() || {};

                    // If hide column
                    if (this.elems()[index].googleColumn == index) {
                        // Hide column
                        this.elems()[index].googleColumn = {
                            label: this.elems()[index].label,
                            type: this.elems()[index].chartType,
                            calc: function () {
                                return null;
                            }
                        };
                        // Coloring serie to gray
                        this.elems()[index-1].googleSerie.color = '#cccccc';
                        this.elems()[index].displayOnChartAfterLoad = false;
                    } else {
                        this.elems()[index].googleColumn = index;
                        this.elems()[index-1].googleSerie.color = null;
                        this.elems()[index].displayOnChartAfterLoad = true;
                    }

                    chartView.columns = this.getColumnsForGoogleChart();
                    this.chart.setView(chartView);
                    this.chart.draw();
                }
            }
        }
    });
});
