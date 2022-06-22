require(
    [
        'mage/url',
        'jquery',
        'underscore',
        'uiComponent',
        'domReady',
        'https://www.gstatic.com/firebasejs/4.1.3/firebase-app.js',
        'https://www.gstatic.com/firebasejs/4.1.3/firebase.js',
        'https://www.gstatic.com/firebasejs/4.1.3/firebase-messaging.js'
    ],
    function (mageUrl,$, _, Component, domReady) {
        'use strict';

        domReady(function () {
            var self = this;
            var delayTime = 50000;
            var confirm = 0;
            var timeToShowPopup = 0;
            var SenderId = "";
            var contentPopup ="Would you like to subscribe to the newsletter?";
            var askCustomersToAllowWebPushSubscriptions = 0;

            //check this browser is access web or not.

            $.ajax({
                method:"POST",
                dataType: "json",
                url:BASE_URL+"notibox/handleNotification/handleConfirmBox",
                data: {
                    value:"getDelayTime"
                },
                success: function (result){
                    var timeNow = new Date();
                    timeToShowPopup = result.timeShowPopup;
                    delayTime = result.time;
                    SenderId  = result.senderId;
                    askCustomersToAllowWebPushSubscriptions = result.askCustomersToAllowWebPushSubscriptions;
                    if(result.contentPopup){
                        contentPopup = result.contentPopup;
                    }else{
                        contentPopup = 'Would you like to subscribe to the newsletter?';
                    }

                    if ('serviceWorker' in navigator && 'PushManager' in window) {
                        if(Notification.permission === 'default' && askCustomersToAllowWebPushSubscriptions == 1){
                            // Check if customers have pressed "remind me late" button
                            if(localStorage.getItem('timeDefer')!== undefined && localStorage.getItem('timeDefer')!= null){
                                var time = timeNow - localStorage.getItem('timeDefer');
                                timeToShowPopup = delayTime - time;
                            }
                            //show popup
                            setTimeout(function () {
                                $(".mgn-message-allow-subscriber").text(contentPopup);
                                $(".noti-popup").show();
                                localStorage.removeItem('timeDefer');
                            }, timeToShowPopup+1000);
                        }

                        if(Notification.permission === 'granted'){

                            var messaging;
                            navigator.serviceWorker.register(result.urlFirebase)
                                .then(function(registration) {
                                    const firebaseConfig = {
                                        messagingSenderId:SenderId,
                                    };
                                    firebase.initializeApp(firebaseConfig);
                                    messaging = firebase.messaging();
                                    messaging.useServiceWorker(registration);
                                    messaging.onMessage(payload => {
                                        var notificationTitle = payload.notification.title;
                                        var notificationOptions = {
                                            body : payload.notification.body,
                                            icon : payload.notification.icon,
                                            click_action : payload.data.click_action,
                                            tag : payload.data.id
                                        };
                                        //send notification to customer
                                        var notification = new Notification(notificationTitle,notificationOptions);
                                        // handle when click to notification on foreground
                                        notification.onclick = function(event) {
                                            //open notification on current tab
                                            window.open(payload.data.click_action , '_self'); //on new tab = '_blank'
                                        }
                                    });
                                    return messaging.getToken();
                                }).then(function (token){
                                console.log(token);
                                if(localStorage.getItem('magenestFrId') === null){
                                    localStorage.setItem('magenestFrId',Math.random().toString(36).substring(2));
                                }
                                $.ajax({
                                    method:"POST",
                                    dataType: "json",
                                    url: BASE_URL+"notibox/customer/saveToken",
                                    data: {
                                        id: localStorage.getItem('magenestFrId'),
                                        token:token
                                    }
                                })
                            })
                            .catch(function(err) {
                                console.error('Unable to register service worker.', err);
                            });
                        }
                    }
                    else{
                        console.log('browser not support');
                    }
                }
            });
        });
    }
);
