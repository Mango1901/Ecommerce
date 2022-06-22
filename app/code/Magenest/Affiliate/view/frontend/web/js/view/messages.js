

define(
    [
        'ko',
        'Magento_Ui/js/view/messages',
        'Magenest_Affiliate/js/model/messageList'
    ],
    function (ko, Component, globalMessages) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magenest_Affiliate/messages',
                selector: '[data-role=affiliate-coupon-messages]'
            },

            initialize: function (config, messageContainer) {
                this._super(config, messageContainer);
                this.messageContainer = globalMessages;

                return this;
            }
        });
    }
);

