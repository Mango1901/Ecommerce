
define([
    'jquery'
], function ($) {
    "use strict";

    $.widget('magenest.commission', {

        tierElementTmp: '',
        /**
         * This method constructs a new widget.
         * @private
         */
        _create: function () {
            this.initDeleteCommission();
            this.initAddTier();
            this.tierElementTmp = $('#commission_tiers_table tr#1').clone();
        },
        /**
         * @returns {magenest.commission}
         */
        initDeleteCommission: function () {
            $(".delete-commission-option").live('click', function (event) {
                $(this).parent().parent().remove();
            });

            return this;
        },
        /**
         * @returns {magenest.commission}
         */
        initAddTier: function () {
            var self = this;
            $('#commission-add').click(function () {
                var tier = $('#commission_tiers_table tr:last').clone();
                if (!tier[0].id) {
                    tier = self.tierElementTmp;
                    tier[0].id = 0;
                }
                var tierNumber = tier[0].id = Number(tier[0].id) + 1;
                var tierName = tier.children('td').children('.tier-number').text();
                tierName = tierName.split(" ");
                tierName = tierName[0] + " " + tierNumber;
                var commissionTierNumber = 'commission[tier_' + tierNumber + ']';
                tier.children('td').children('.tier-number').text(tierName);
                tier.children('td:nth-child(2)').children('select').attr('name', commissionTierNumber + '[type]');
                tier.children('td:nth-child(3)').children('input')
                    .attr('name', commissionTierNumber + '[value]')
                    .val("");
                tier.children('td:nth-child(4)').children('select').attr('name', commissionTierNumber + '[type_second]');
                tier.children('td:nth-child(5)').children('input')
                    .attr('name', commissionTierNumber + '[value_second]')
                    .val("");
                $('#commission_tiers_table tr:last').after(tier);
            });

            return this;
        }

    });

    return $.magenest.commission;
});

