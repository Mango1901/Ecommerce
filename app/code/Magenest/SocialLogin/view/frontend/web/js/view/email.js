/**
 * Created by bao on 26/06/2017.
 */
define([
    'jquery',
    'uiComponent',
    'ko',
    'Magenest_SocialLogin/js/sociallogin',
    'Magenest_SocialLogin/js/view/modal',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/action/check-email-availability',
    'Magento_Customer/js/action/login',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/url',
    'mage/validation'
], function ($, Component, ko,sociallogin, modal, customer, checkEmailAvailability, loginAction, quote, checkoutData, fullScreenLoader, urlBuilder) {
    'use strict';

    var validatedEmail = checkoutData.getValidatedEmailValue();

    if (validatedEmail && !customer.isLoggedIn()) {
        quote.guestEmail = validatedEmail;
    }

    return Component.extend({
        defaults: {
            template: 'Magenest_SocialLogin/email',
            email: checkoutData.getInputFieldEmailValue(),
            emailFocused: false,
            isLoading: false,
            isPasswordVisible: false,
            isSocialNetworkUsed: false,
            isFacebookUsed : false,
            isGoogleUsed : false,
            isAppleUsed: false,

            isButtonEnabledCheckout : window.modal_content.isButtonEnabledCheckout,
            listens: {
                email: 'emailHasChanged',
                emailFocused: 'validateEmail'
            }
        },
        checkDelay: 2000,
        checkRequest: null,
        isEmailCheckComplete: null,
        isCustomerLoggedIn: customer.isLoggedIn(),
        registerUrl: window.checkoutConfig.registerUrl,
        forgotPasswordUrl: window.checkoutConfig.forgotPasswordUrl,
        emailCheckTimeout: 0,
        googleUrl : window.modal_content.GoogleUrl,
        facebookUrl : window.modal_content.FacebookUrl,
        appleUrl : window.modal_content.AppleUrl,
        isFacebookEnabled: ko.observable(window.modal_content.isFacebookEnabled),
        isGoogleEnabled : ko.observable(window.modal_content.isGoogleEnabled),
        isAppleEnabled : ko.observable(window.modal_content.isAppleEnabled),
        isEnabledLoginWithTelephone : window.checkoutConfig.isEnabledLoginWithTelephone,

        /**
         * Initializes observable properties of instance
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe(['email', 'emailFocused', 'isLoading','isButtonEnabledCheckout','isPasswordVisible','isSocialNetworkUsed','isFacebookUsed','isGoogleUsed', 'isAppleUsed']);
            return this;
        },

        /**
         * Callback on changing email property
         */
        emailHasChanged: function () {
            var self = this;
            self.refreshSocialLogin();
            // clearTimeout(this.emailCheckTimeout);

            if (self.validateEmail()) {
                quote.guestEmail = self.email();
                checkoutData.setValidatedEmailValue(self.email());
            }
            // this.emailCheckTimeout = setTimeout(function () {
            //     if (self.validateEmail()) {
            //         self.checkEmailAvailability();
            //         if(self.isButtonEnabledCheckout() == 1){
            //             self.checkSocialNetworkAvailability();
            //         } else self.isSocialNetworkUsed(false);
            //     } else {
            //         self.isPasswordVisible(false);
            //         self.isSocialNetworkUsed(false);
            //         $('.phone-login').show();
            //         $('#customer-email-fieldset').addClass('fieldset-login-phone')
            //     }
            // }, self.checkDelay);

            checkoutData.setInputFieldEmailValue(self.email());
        },

        /**
         * Check email existing.
         */
        checkEmailAvailability: function () {
            var self = this;
            this.validateRequest();
            this.isEmailCheckComplete = $.Deferred();
            this.isLoading(true);
            this.checkRequest = checkEmailAvailability(this.isEmailCheckComplete, this.email());

            $.when(this.isEmailCheckComplete).done(function () {
                self.isPasswordVisible(false);
                $('.phone-login').show();
                $('#customer-email-fieldset').addClass('fieldset-login-phone')
            }).fail(function () {
                self.isPasswordVisible(true);
                $('.phone-login').hide();
                $('#customer-email-fieldset').removeClass('fieldset-login-phone')
            }).always(function () {
                self.isLoading(false);
            });
        },

        /**
         * If request has been sent -> abort it.
         * ReadyStates for request aborting:
         * 1 - The request has been set up
         * 2 - The request has been sent
         * 3 - The request is in process
         */
        validateRequest: function () {
            if (this.checkRequest != null && $.inArray(this.checkRequest.readyState, [1, 2, 3])) {
                this.checkRequest.abort();
                this.checkRequest = null;
            }
        },

        /**
         * Local email validation.
         *
         * @param {Boolean} focused - input focus.
         * @returns {Boolean} - validation result.
         */
        validateEmail: function (focused) {
            var loginFormSelector = 'form[data-role=email-with-possible-login]',
                usernameSelector = loginFormSelector + ' input[name=username]',
                loginForm = $(loginFormSelector),
                validator;

            loginForm.validation();

            if (focused === false && !!this.email()) {
                return !!$(usernameSelector).valid();
            }

            validator = loginForm.validate();

            return validator.check(usernameSelector);
        },

        checkSocialNetworkAvailability : function () {
            var self = this;
            var url = urlBuilder.build('sociallogin/ajaxgettype');
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                cache: false,
                showLoader: true,
                data: {
                    email: self.email()
                },
                success: function (response) {

                    if (response.type != null) {
                        self.isSocialNetworkUsed(true);
                        if (response.type == 'facebook') self.isFacebookUsed(true);
                        if (response.type == 'google') self.isGoogleUsed(true);
                        if (response.type == 'apple') self.isAppleUsed(true);
                    }
                    else {
                        self.isSocialNetworkUsed(false);
                    }
                }
            });
        },

        refreshSocialLogin: function(){
            var self = this;
            self.isFacebookUsed(false);
            self.isGoogleUsed(false);
            self.isAppleUsed(false);
        },

        /**
         * Log in form submitting callback.
         *
         * @param {HTMLElement} loginForm - form element.
         */
        login: function (loginForm) {
            var loginData = {},
                formDataArray = $(loginForm).serializeArray();

            formDataArray.forEach(function (entry) {
                loginData[entry.name] = entry.value;
            });

            if (this.isPasswordVisible() && $(loginForm).validation() && $(loginForm).validation('isValid')) {
                fullScreenLoader.startLoader();
                loginAction(loginData).always(function() {
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
            return urlBuilder.build('customer/account/create');
        },
    });
});
