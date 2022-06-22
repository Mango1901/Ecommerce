/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([ // jshint ignore:line
    'jquery',
    "underscore",
    'uiComponent',
    'ko',
    'uiRegistry',
    'mage/translate',
    'Magento_Checkout/js/model/quote',
    'mage/template',
    'niceSelect'
], function ($, _, Component, ko, registry, $t, niceSelect) {
    'use strict';

    window.addEventListener('DOMContentLoaded', (event) => {
        console.log($('select[name="city_id"]'));
    });

    return Component.extend({
        defaults: {
            listens: {
                cityField: 'handleChangesCity',
                districtField: 'handleChangesDistrict',
                wardField: 'handleChangesWard',
            }
        },
        /**
         * Initialize
         *
         * @returns {exports.initialize}
         */
        initialize: function () {
            var self = this;
            this.isOpenDirectory = ko.observable(false);
            this.cityName = ko.observable('');
            this.districtName = ko.observable('');
            this.wardName = ko.observable('');

            this.fullDirectoryInformation = ko.computed(function () {
                let name = '';

                if (self.wardName()) {
                    name += self.wardName() + ', ';
                }

                if (self.districtName()) {
                    name += self.districtName() + ', ';
                }

                if (self.cityName()) {
                    name += self.cityName();
                }

                return name;
            });

            this._super();
            return this;
        },

        clickOpenDirectory: function () {
            var self = this;
            self.isOpenDirectory(!self.isOpenDirectory());
        },

        handleChangesCity: function (value) {
            this.cityName(value);
        },

        handleChangesDistrict: function (value) {
            this.districtName(value);
        },

        handleChangesWard: function (value) {
            this.wardName(value);
        }
    });
});
