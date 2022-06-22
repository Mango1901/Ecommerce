/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'niceSelect'
], function ($, _, registry, Select, niceSelect) {
    'use strict';
    $('.city').niceSelect();

    return Select.extend({
        defaults: {
            skipValidation: false,
            captionValue: 'tn',
            imports: {
                update: '${ $.parentName }.country_id:value'
            }
        },

        /**
         * Set region to customer address form
         */
        setDifferedFromDefault: function () {
            var self = this;
            this._super();

            _.each(['city', 'region_id', 'region_id_input'], function (field) {
                registry.async(self.parentName + '.' + field)(function (element) {
                    element.setVisible(false);
                    if (field == "city") {
                        element.value(self.indexedOptions[self.value()].full_name);
                    }
                });
            });
        },

        /**
         * Callback that fires when 'value' property is updated.
         */
        onUpdate: function () {
            var self = this,
                option = _.find(this.options(), function (item) {
                    return item.value == self.value();
                });

            if(!_.isUndefined(option)){
                this.source.set(this.dataScope.replace('_id', ''), option.full_name);
            }
            registry.async(self.parentName + '.city')(function (element) {
                element.setVisible(false);
                element.value(self.indexedOptions[self.value()].full_name);
            });
        },

        initNiceSelect: function () {
            $('select[name="city_id"]').niceSelect();
        }
    });
});

