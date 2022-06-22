/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/form/components/html'
], function ($, $t, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            label: '',

            listens: {
                titleKey: 'handleChanges'
            }
        },

        /**
         * Handle Change
         */
        handleChanges: function (newValue) {
            this.updateContent($t('Length Of %1: %2/255').replace('%1', this.label).replace('%2', newValue.length))
        },
    });
});
