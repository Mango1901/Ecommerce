/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
], function (_, registry, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            skipValidation: false,
            captionValue: 'tn',
            imports: {
                update: '${ $.parentName }.district_id:value'
            }
        },

        /**
         * Set region to customer address form
         */
        setDifferedFromDefault: function () {
            this._super();

            registry.async(this.parentName + '.' + 'ward')(function (element) {
                element.setVisible(false);
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
            if(this.customScope === "shippingAddress") {
                var data = Object.assign({}, this.source.shippingAddress);
                _.each(['city', 'city_id', 'district', 'district_id', 'ward', 'ward_id'], function (attribute) {
                    if (data.hasOwnProperty(attribute)) {
                        data['custom_attributes'][attribute] = {
                            'attribute_code': attribute,
                            'value': data[attribute]
                        }
                    }
                });
                quote.shippingAddress()['customAttributes'] = data['custom_attributes'];
                quote.shippingAddress()['city'] = data['city']

                var billingNotSaveInBook = {...quote.shippingAddress()}
                billingNotSaveInBook.saveInAddressBook = 0;
                quote.billingAddress(billingNotSaveInBook);
            }
        }

    });
});

