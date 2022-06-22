
/**
 * @returns {string}
 */
function getCreateChart() {
    var prefix = document.getElementsByClassName("mp_menu").length ? '' : 'fake-';
    return 'Magenest_AffiliateOpt/js/' + prefix + 'create-chart';
}

define([
    'jquery',
    'Magento_Ui/js/grid/provider',
    getCreateChart()
], function ($, Provider, createChart) {
    'use strict';

    return Provider.extend({
        reload: function (options) {
            this.addParamsToFilter();
            this._super();
        },
        /**
         * Compatible with Magenest Reportspro
         */
        addParamsToFilter: function () {
            if (this.isEnableReportMenu()) {
                $('.admin__form-field:has(input[name="created_at[from]"])').hide();
                $('.admin__form-field:has(select[name="store_id"])').hide();
                $('.admin__form-field:has(select[name="period"])').hide();
                var mpFilter = (typeof this.params.mpFilter === "undefined") ? {} : this.params.mpFilter;
                if (typeof mpFilter.startDate === "undefined") {
                    mpFilter.startDate = $('#daterange').data().startDate.format('Y-MM-DD');
                }
                if (typeof mpFilter.endDate === "undefined") {
                    mpFilter.endDate = $('#daterange').data().endDate.format('Y-MM-DD');
                }

                if (typeof mpFilter.store === "undefined") {
                    mpFilter.store = $('#store_switcher').val();
                }
                this.params.mpFilter = mpFilter;
            }
        },
        /**
         * @param data
         * @returns {*}
         */
        processData: function (data) {
            if ($(".affiliate-transaction-index").length > 0) {
                this.buildChart(data);
            }

            return this._super();
        },
        /**
         * Build chart when Mp Reports enable
         */
        buildChart: function (data) {
            if (this.isEnableReportMenu()) {
                var items = data.items;
                if (Object.keys(items).length) {
                    var rewardData = [0, 0, 0];
                    _.each(items, function (record, index) {
                        var status = record.status;
                        if (status) {
                            var key = Number(status) - 2;
                            rewardData[key] += parseFloat(record.amount_report)
                        }
                    });

                    createChart({
                        chartData: {
                            labelColor: this.labelColor,
                            priceFormat: this.priceFormat,
                            data: rewardData,
                            maintainAspectRatio: true
                        },
                        chartElement: 'transaction-chart'
                    });
                    $('#transaction-chart').show();
                } else {
                    $('#transaction-chart').hide();
                }
            }
        },
        /**
         * @returns {jQuery}
         */
        isEnableReportMenu: function () {
            return $('.mp_menu').length;
        }
    });
});
