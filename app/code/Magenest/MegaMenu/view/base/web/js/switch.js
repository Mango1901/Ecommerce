/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

require([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/dataPost',
    'Magento_Ui/js/modal/confirm',
    'jquery/ui',
    'mage/translate'
], function ($, modal, dataPost, confirmModal) {
    'use strict';

    /**
     * Helper for onclick action.
     * @param {String} message
     * @param {String} url
     * @param {Object} postData
     * @returns {Boolean}
     */
    window.switchVersionAction = function (message, url, postData) {
        var options = {
            title: message,
            type: 'popup',
            responsive: true,
            modalClass: 'switch-version-modal',
            innerScroll: true,
            buttons: [
                {
                    text: $.mage.__('Cancel'),
                    class: 'action-secondary action-dismiss',
                    click: function () {
                        popup.modal('closeModal');
                    }
                },
                {
                    text: $.mage.__('Switch'),
                    class: 'action-primary action-accept',
                    click: function () {
                        if (postData !== undefined) {
                            var selectVersionMsg = $('#switch-version-error');
                            selectVersionMsg.hide();
                            var selectedVersion = $('select#switch-version').val();
                            if (selectedVersion === undefined || selectedVersion === "" || selectedVersion === null) {
                                selectVersionMsg.show();
                                return;
                            }
                            var selectVersionForm = $('#switch-version-form');

                            if (selectVersionForm.validation() && selectVersionForm.validation('isValid')) {
                                postData.data.selectedVersion = selectedVersion;
                                postData.action = url;
                                $('body').trigger('processStart');
                                dataPost().postData(postData);
                                popup.modal('closeModal');
                            }
                        } else {
                            setLocation(url);
                        }
                    }
                }
            ]
        };
        var popup = $('#switch-version-popup-data').modal(options);
        popup.modal('openModal');

        return false;
    };
});
