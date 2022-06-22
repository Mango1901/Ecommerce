/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'Magento_Ui/js/form/form',
    'Magento_Customer/js/action/login',
    'Magento_Customer/js/customer-data',
    'Magento_Customer/js/model/authentication-popup',
    'mage/translate',
    'mage/url',
    'Magento_Ui/js/modal/alert',
    'Magenest_SocialLogin/js/sociallogin',
    'mage/validation'
], function ($, ko, Component, loginAction, customerData, authenticationPopup, $t, url, alert, sociallogin) {
    'use strict';

    return Component.extend({
        registerUrl: window.authenticationPopup.customerRegisterUrl,
        forgotPasswordUrl: window.authenticationPopup.customerForgotPasswordUrl,
        autocomplete: window.authenticationPopup.autocomplete,
        modalWindow: null,
        isLoading: ko.observable(false),

        facebookUrl : window.modal_content.FacebookUrl,
        isFacebookEnabled: ko.observable(window.modal_content.isFacebookEnabled),
        googleUrl : window.modal_content.GoogleUrl,
        isGoogleEnabled : ko.observable(window.modal_content.isGoogleEnabled),
        appleUrl : window.modal_content.AppleUrl,
        isAppleEnabled : ko.observable(window.modal_content.isAppleEnabled),

        defaults: {
            template: 'Magenest_SocialLogin/authentication-popup'
        },

        /**
         * Init
         */
        initialize: function () {
            var self = this;

            this._super();
            url.setBaseUrl(window.authenticationPopup.baseUrl);
            loginAction.registerLoginCallback(function () {
                self.isLoading(false);
            });
        },

        /** Init popup login window */
        setModalElement: function (element) {
            if (authenticationPopup.modalWindow == null) {
                authenticationPopup.createPopUp(element);
            }
        },

        /** Is login form enabled for current customer */
        isActive: function () {
            var customer = customerData.get('customer');

            return customer() == false; //eslint-disable-line eqeqeq
        },

        /** Show login popup window */
        showModal: function () {
            if (this.modalWindow) {
                $(this.modalWindow).modal('openModal');
            } else {
                alert({
                    content: $t('Guest checkout is disabled.')
                });
            }
        },

        /**
         * Provide login action
         *
         * @return {Boolean}
         */
        login: function (formUiElement, event) {
            var loginData = {},
                formElement = $(event.currentTarget),
                formDataArray = formElement.serializeArray();

            event.stopPropagation();
            formDataArray.forEach(function (entry) {
                loginData[entry.name] = entry.value;
            });

            if (formElement.validation() &&
                formElement.validation('isValid')
            ) {
                this.isLoading(true);
                loginAction(loginData);
            }

            return false;
        },

        getFacebookUrl : function () {
            var self = this;
            sociallogin.display(self.facebookUrl,'Facebook',600,600);
            self.reLoadMiniCart();
        },

        getGoogleUrl : function () {
            var self = this;
            sociallogin.display(self.googleUrl,'Google',600,600);
            self.reLoadMiniCart();
        },

        getAppleUrl : function () {
            var self = this;
            sociallogin.display(self.appleUrl,'Apple',600,600);
            self.reLoadMiniCart();
        },

        reLoadMiniCart: function () {
            var sections = ['cart'];
            customerData.invalidate(sections);
            customerData.reload(sections, true);
        }

    });
});
