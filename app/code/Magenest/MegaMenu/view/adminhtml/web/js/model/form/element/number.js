/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Ui/js/form/element/abstract',
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            elementTmpl: 'Magenest_MegaMenu/form/element/number-input',
        }
    });
});
