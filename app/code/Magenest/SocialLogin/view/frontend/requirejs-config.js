var config = {
    paths: {
        slick: 'Magenest_SocialLogin/js/slick',
        jszip: 'Magenest_SocialLogin/js/jszip.min',
        'jszip-utils': 'Magenest_SocialLogin/js/jszip-utils.min',
        fileSaver: 'Magenest_SocialLogin/js/FileSaver.min',
    },
    map: {
        '*': {
            magnificPopup: 'Magenest_SocialLogin/js/jquery.magnific-popup',
            magenestSocialLogin: 'Magenest_SocialLogin/js/sociallogin',
            hideshare: 'Magenest_SocialLogin/js/hideshare',
            magenestModal: 'Magenest_SocialLogin/js/view/modal',
            magenestAuthenticationPopup: 'Magenest_SocialLogin/js/view/authentication-popup',
            magenestAuthentication: 'Magenest_SocialLogin/js/view/authentication',
            magenestEmail: 'Magenest_SocialLogin/js/view/email'
        }
    },
    shim: {
        magnificPopup: {
            deps: ['jquery']
        },
        hideshare: {
            deps: ['jquery']
        },
        magenestAuthentication: {
            deps: ['jquery']
        },
        magenestAuthenticationPopup: {
            deps: ['jquery']
        },
        magenestEmail: {
            deps: ['jquery']
        },
        magenestModal: {
            deps: ['jquery']
        },
        magenestSocialLogin: {
            deps: ['jquery']
        },
        slick: {
            deps: ['jquery']
        }
    }
};
