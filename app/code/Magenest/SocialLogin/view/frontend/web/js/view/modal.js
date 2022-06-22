/*global define*/
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
        facebookUrl: window.modal_content.FacebookUrl,
        isFacebookEnabled: ko.observable(window.modal_content.isFacebookEnabled),
        googleUrl: window.modal_content.GoogleUrl,
        isGoogleEnabled: ko.observable(window.modal_content.isGoogleEnabled),
        isAppleEnabled: ko.observable(window.modal_content.isAppleEnabled),
        appleUrl: window.modal_content.AppleUrl,
        forgotPasswordUrl: window.authenticationPopup.customerForgotPasswordUrl,
        createNewAccount: window.authenticationPopup.customerRegisterUrl,
        autocomplete: window.checkout.autocomplete,
        modalWindow: null,

        messageLogin: ko.observable(""),

        isEnabledPopup: ko.observable(window.modal_content.isEnabledPopup),

        prefixElementHtml: 'social-login-popup' + '-',

        isLoading: ko.observable(false),
        defaults: {
            template: 'Magenest_SocialLogin/modal_content'
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
                authenticationPopup.modalWindow = element;
            }
        },

        /** Is login form enabled for current customer */
        isActive: function () {
            var customer = customerData.get('customer');

            return customer() === false;
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

        /** Provide login action */
        login: function (formUiElement, event) {
            var self = this;
            this.messageLogin("");
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
                loginAction(loginData).always(function (response) {
                    if (response.errors) {
                        self.messageLogin(response.message);
                    }
                });
            } else {
                this.messageLogin("You did not sign in correctly or your account is temporarily disabled.");
            }
            return false;
        },

        isMessage: function () {
            return this.messageLogin() !== "";
        },

        getFacebookUrl: function () {
            var self = this;
            sociallogin.display(self.facebookUrl, 'Facebook', 600, 600);
            self.reLoadMiniCart();
        },

        getGoogleUrl: function () {
            var self = this;
            sociallogin.display(self.googleUrl, 'Google', 600, 600);
            self.reLoadMiniCart();
        },

        getAppleUrl: function () {
            var self = this;
            sociallogin.display(self.appleUrl, 'Apple', 600, 600);
            self.reLoadMiniCart();
        },

        reLoadMiniCart: function () {
            var sections = ['cart'];
            customerData.invalidate(sections);
            customerData.reload(sections, true);
        }
    });
});
