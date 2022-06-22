require([
    'jquery'
], function ($) {
    $(document).ready(function() {
        if (/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i.test(navigator.userAgent)) {
            $('.mobile_bar').css('display', 'block');
            $('.desktop_bar').css('display', 'none');
        } else {
            $('.desktop_bar').css('display', 'block');
            $('.mobile_bar').css('display', 'none');
        }
    });
});