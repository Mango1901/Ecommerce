define(
    [
        'jquery',
        'uiComponent',
        'ko',
        'underscore',
        'mageUtils'
    ],
    function ($, Component, ko, _, utils) {
        'use strict';
        return Component.extend({
            default: {
                template: {
                    name: "Magenest_NotificationBox/notification"
                },
                allNotification: null,
                urlNotificationTab: null,
                description: null,
                titleBackgroundColorValue: null,
                visibleNotification: false,
                totalNotificationUnread: 0,
                visibleNotificationBox:false,
                boxPosition:'',
                boxWidth: '300px'
            },
            initObservable: function () {
                this._super()
                    .observe([
                        'allNotification',
                        'urlNotificationTab',
                        'description',
                        'titleBackgroundColorValue',
                        'visibleNotification',
                        'totalNotificationUnread',
                        'visibleNotificationBox',
                        'boxPosition',
                        'boxWidth'
                    ]);
                return this;
            },
            initialize: function () {
                var self = this;
                this._super();
                self.urlNotificationTab(self.url);
                self.titleBackgroundColorValue(self.backgroundColorValue);

                $.ajax({
                    method:"POST",
                    dataType: "json",
                    url:BASE_URL+"notibox/customer/getNotificationData",
                    success: function (result){
                       if(!result['customerNotLogin']){
                           $('.notificationBox').show();
                           self.visibleNotificationBox(true);
                           if(result['unreadNotification']<100) {
                               self.totalNotificationUnread(result['unreadNotification']);
                           }else{
                               self.totalNotificationUnread('99+');
                           }
                           self.boxPosition(self.boxPositionClass + " magenest-notification-box");
                           self.boxWidth(self.notificationBoxWidth + "px");

                           if(!result['allNotification'].length){
                               self.visibleNotification(true);
                           }
                           else{
                               self.allNotification(result['allNotification']);
                           }
                       }
                    }
                });

                var $win = $(window); // or $box parent container
                var $box = $(".magenest-notification");

                $win.on("click.Bst", function(event){
                    if (
                        $box.has(event.target).length === 0 //checks if descendants of $box was clicked
                        &&
                        !$box.is(event.target) //checks if the $box itself was clicked
                    ){
                        $(".magenest-notification-box").hide();
                    } else {
                        $(".magenest-notification-box").show();
                    }
                });
            }
        });
    }
);
