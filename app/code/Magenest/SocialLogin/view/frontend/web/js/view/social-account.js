define([
    'jquery',
    'mage/url',
    'Magento_Ui/js/modal/confirm',
    'mage/translate'
], function ($, url, confirm, $t) {
    'use strict';
    return function () {
        $(".btnUnlink-social-login").click(function () {
            var self = this;
            confirm({
                title: $t('Unlink Confirmation'),
                content: 'This account will be unlinked! Are you sure?',
                actions: {
                    confirm: function(){
                        var action = url.build('sociallogin/customer/unlink');
                        var posting = $.post(action, {type: self.getAttribute('socialType')});
                        $('body').loader('show');
                        posting.done(function (data) {
                            window.location.href = $(location).attr('href');
                            $('body').loader('hide');
                            window.location.reload();
                        });
                    },
                    cancel: function(){},
                    always: function(){}
                }
            });
        });
        $("#btn-request-password").click(function () {
            window.location.href = url.build('sociallogin/customer/requestPassword/');
        });
    }
});
