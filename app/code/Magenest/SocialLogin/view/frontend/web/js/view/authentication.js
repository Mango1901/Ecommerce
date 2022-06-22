define(
    [
        'jquery',
        'ko',
        'mage/url',
        'Magento_Ui/js/form/form',
        'Magento_Customer/js/action/login',
        'Magento_Customer/js/model/customer',
        'Magenest_SocialLogin/js/sociallogin',
        'mage/validation',
        'Magento_Checkout/js/model/authentication-messages',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function($, ko, url , Component, loginAction, customer, sociallogin,  validation, messageContainer, fullScreenLoader) {
        'use strict';
        var checkoutConfig = window.checkoutConfig;
        return Component.extend({
            isGuestCheckoutAllowed: checkoutConfig.isGuestCheckoutAllowed,
            isCustomerLoggedIn: checkoutConfig.isCustomerLoggedIn,
            registerUrl: checkoutConfig.registerUrl,
            forgotPasswordUrl: checkoutConfig.forgotPasswordUrl,
            createNewAccount: checkoutConfig.registerUrl,
            autocomplete: checkoutConfig.autocomplete,
            facebookUrl : window.modal_content.FacebookUrl,
            isFacebookEnabled: ko.observable(window.modal_content.isFacebookEnabled),
            googleUrl : window.modal_content.GoogleUrl,
            isGoogleEnabled : ko.observable(window.modal_content.isGoogleEnabled),
            appleUrl : window.modal_content.AppleUrl,
            isAppleEnabled : ko.observable(window.modal_content.isAppleEnabled),

            isButtonEnabledCheckout : ko.observable(window.modal_content.isButtonEnabledCheckout),
            defaults: {
                template: 'Magenest_SocialLogin/authentication'
            },
            /** Is login form enabled for current customer */
            isActive: function() {
                return !customer.isLoggedIn();
            },

            /** Provide login action */
            login: function(loginForm) {
                var loginData = {},
                    formDataArray = $(loginForm).serializeArray();

                formDataArray.forEach(function (entry) {
                    loginData[entry.name] = entry.value;
                });

                if($(loginForm).validation()
                    && $(loginForm).validation('isValid')
                ) {
                    fullScreenLoader.startLoader();
                    loginAction(loginData, checkoutConfig.checkoutUrl, undefined, messageContainer).always(function() {
                        fullScreenLoader.stopLoader();
                    });
                }
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
            },

            gotoCreateAccount: function() {
                return url.build('customer/account/create');
            },
        });
    }
);

