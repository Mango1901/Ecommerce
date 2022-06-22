

define([
    "jquery",
    "mage/translate",
    "Magento_Ui/js/modal/modal"
], function ($, $t, modal) {
    'use strict';

    $.widget('affiliate.banner', {

        _create: function () {
            var bannerId        = this.options.bannerId,
                elbannerIdClick = '#' + bannerId,
                elpopup         = '.bnlink-refer-' + bannerId;

            var modalOption = {
                'type': 'popup',
                'title': $t('Link and Script Refer Banner'),
                'modalClass': 'mp-affiliate-banner',
                'responsive': true,
                'innerScroll': true,
                'trigger': elbannerIdClick,
                'buttons': []
            };

            modal(modalOption, elpopup);
        }
    });

    return $.affiliate.banner;
});
