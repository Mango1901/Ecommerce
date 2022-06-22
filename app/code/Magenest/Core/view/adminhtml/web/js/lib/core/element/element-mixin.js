/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'uiRegistry',
    'mage/translate'
], function ($, _, uiRegistry) {
    'use strict';

    /**
     * Parse provided data.
     * @param {String} data
     * @returns {Boolean|Object}
     */
    function parseData(data) {
        if (typeof data !== 'string') {
            return false;
        }

        data = data.split(':');

        if (!data[0]) {
            return false;
        }

        return {
            target: data[0],
            property: data[1]
        };
    }

    return function (original) {
        return original.extend({
            initLinks: function () {
                var self = this;

                if (this['dependency'] && self.visible()) {
                    _.map(this['dependency'], function (valueList, target) {
                        target = parseData(target);
                        uiRegistry.async(target.target)(function (dependField) {
                            self.visible((valueList.indexOf(dependField.value()) != -1))
                            dependField.on(target.property, function (value) {
                                self.visible((valueList.indexOf(value) != -1))
                            });
                        });
                    })
                }

                return this.setListeners(this.listens)
                    .setLinks(this.links, 'imports')
                    .setLinks(this.links, 'exports')
                    .setLinks(this.exports, 'exports')
                    .setLinks(this.imports, 'imports');
            }
        });
    };
});