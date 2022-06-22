/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ProductLabel
 */
define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
], function (_, uiRegistry, select) {
    'use strict';

    return select.extend({
        defaults: {
            use_date_range: false,
            exports: {
                'from_date': 'index=from_date:visible',
                'to_date': 'index=to_date:visible',
            },
            links: {
                'from_date': 'use_date_range',
                'to_date': 'from_date'
            }
        },

        initObservable: function () {
            this._super().observe([
                'use_date_range'
            ]);
            return this;
        },

        initialize: function () {
            var self = this;
            this._super();
            self.use_date_range(this.value() == 1);

            this.value.subscribe(function (value) {
                self.use_date_range(value == 1);
            }, self);
            return this;
        }
    });
});
