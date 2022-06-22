/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/abstract',
], function ($, _, Element) {
    return Element.extend({
        initialize: function () {
            var self = this;
            self._super();
            $('body').on('change', '#customer-telephone', function (value) {
                self.value($(this).val());
            });
        },

        populateTelephone: function () {
            if (this.value()) {
                $('#customer-telephone').val(this.value());
            }
        }
    });
});
