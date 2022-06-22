/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'jquery',
    'Magento_PageBuilder/js/events',
    'consoleLogger'
], function (_, $, events, consoleLogger) {
    'use strict';

    return function (target) {
        return target.extend({

            getTitle: function () {
                var title = this._super();

                var titleArray = title.split(", ");

                return titleArray.join("<br class='show'/>");
            }
        });
    };
});
