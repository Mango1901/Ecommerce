/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license sliderConfig is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

define([
    'Magento_Ui/js/form/element/abstract'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            isUseDefault: false,
            isUseConfig: false,
            listens: {
                'isUseConfig': 'toggleElement',
                'isUseDefault': 'toggleElement'
            }
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            return this._super()
                .observe('isUseConfig');
        },

        /**
         * Toggle element
         */
        toggleElement: function () {
            this.disabled(this.isUseDefault() || this.isUseConfig());
        }
    });
});

