define([
    'jquery',
    'Magento_Catalog/js/price-utils',
    'chartjs'
], function ($, priceUtils) {
    'use strict';
    $.widget('magenest.affiliateChart', {
        _create: function () {
            var chartData = this.options.chartData;
            var data = {
                type: this.options.chartData.type !== undefined ? this.options.chartData.type : 'pie',
                data: {
                    labels: chartData.labelColor.labels,
                    datasets: [{
                        data: chartData['data'],
                        fill: true,
                        backgroundColor: chartData.labelColor.colors !== undefined ? chartData.labelColor.colors : 'rgba(0, 0, 0, 0.1)',
                        borderColor: chartData.borderColor !== undefined ? chartData.borderColor : 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1,
                        label: this.options.chartData.datasetLabel !== undefined ? this.options.chartData.datasetLabel : ''
                    }]
                },
                options: {
                    legend: {
                        display: true,
                        position: this.options.chartData.position !== undefined ? this.options.chartData.position : 'left'
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var dataset = data.datasets[tooltipItem.datasetIndex];
                                var index = tooltipItem.index;

                                return data.labels[index] + ': ' + (chartData.priceFormat ? priceUtils.formatPrice(dataset.data[index], chartData.priceFormat) : dataset.data[index]);
                            }
                        }
                    }
                }
            };

            if (typeof window[this.options.chartElement] !== 'undefined' && typeof window[this.options.chartElement].destroy === 'function') {
                window[this.options.chartElement].destroy();
            }

            if (this.options.chartData.isCompare == 1) {
                var compareDataset = {
                    data: chartData['compareData'],
                    fill: true,
                    backgroundColor: chartData.labelColor.colors,
                    borderWidth: 1
                };
                data.data.datasets.push(compareDataset);
            }

            window[this.options.chartElement] = new Chart($('#' + this.options.chartElement), data);
        }
    });

    return $.magenest.affiliateChart;
});
