/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ProductLabel
 */
define([
    'jquery',
    'ko',
    'Magento_Ui/js/form/element/abstract',
], function ($, ko, Abstract) {
    'use strict';
    return Abstract.extend({

        initialize: function () {
            this._super();
            this.chosenPosition = this.value;
            return this;
        },
        chosenPosition: ko.observable(),
        availablePositions: [
            'top-left', 'top-mid', 'top-right',
            'mid-left', 'mid-mid', 'mid-right',
            'bot-left', 'bot-mid', 'bot-right'],

        tdClick: function (td) {
            var self = this;
            self.chosenPosition(self.availablePositions[td]);
            self.value(self.chosenPosition());
        },
    });
});
