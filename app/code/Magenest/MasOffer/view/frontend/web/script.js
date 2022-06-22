
        function getFromUrl (name, url) { // Lấy các param từ tracking link
        if (!url) url = location.href;
        name        = name.replace (/[\[]/, "\\\[").replace (/[\]]/, "\\\]");
        var regexS  = "[\\?&]" + name + "=([^&#]*)";
        var regex   = new RegExp (regexS);
        var results = regex.exec (url);
        return results == null ? null : results[1];
    }

        function getCookie (cname) {// Lấy nhận cookie
        var name = cname + "=";
        var ca   = document.cookie.split (';');
        for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt (0) == ' ') c = c.substring (1);
        if (c.indexOf (name) == 0) return c.substring (name.length, c.length);
    }
        return undefined;
    }

        function setCookie (key, value, e) { // Ghi nhận, cài đặt thời gian lưu cookie
        var d = new Date ();
        d.setTime (d.getTime () + ( e * 24 * 60 * 60 * 1000 ));
        var ee = "expires=" + d.toUTCString ();
        document.cookie = key + "=" + value + "; " + ee + "; path=/";
    }

        function moTrack () { // Lấy giá trị cho cookie đến từ MasOffer
        var trafficIdUrl = getFromUrl ("traffic_id");
        var refUrl = getFromUrl ("utm_source");
        if (refUrl !== 'masoffer') {
        return false;
    }
        if (trafficIdUrl) {
        setCookie ("mo_traffic_id", trafficIdUrl, 30);
        setCookie ("mo_network", refUrl, 30);
        return true;
        }
        return false;
    }
        moTrack ();
