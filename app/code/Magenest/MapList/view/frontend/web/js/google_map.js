require([
    'jquery',
    'https://cdn.jsdelivr.net/npm/@goongmaps/goong-js@1.0.6/dist/goong-js.js',
    'mage/url'
], function ($, goongjs, urlBuilder) {
    goongjs.accessToken = window.ApiKey;
    var markers = [];
    var info_window;
    var bounds;
    var marker;

    var map = new goongjs.Map({
        container: 'map',
        style: 'https://tiles.goong.io/assets/goong_map_web.json', // stylesheet location
        zoom: 4.669076801998534,
        center: {lng: 107.14364350000005, lat: 15.188637531525615},
        dragRotate: false,
        touchZoomRotate: false
    });

    bounds = new goongjs.LngLatBounds();

    //add marker from javascript array to map
    for (var i = 0; i < window.js_array.length; i++) {
        var storeImg = window.js_array[i]['store_map_img']['url'];
        if(storeImg == null) {
            storeImg = window.img_marker;
        }
        var el = document.createElement('div');
        el.className = 'marker';
        el.style.backgroundImage =
            'url(' + storeImg + ')';
        el.style.width = '64px';
        el.style.height = '64px';

        info_window = new goongjs.Popup({offset: 25}).setMaxWidth("550px");
        info_window.setHTML(getContentOfInfoWindow(
            window.js_array[i]['location_id'],
            window.js_array[i]['name'],
            window.js_array[i]['description'],
            null,
            window.js_array[i]['detail_address'],
            window.js_array[i]['phone']
        ));

        marker = new goongjs.Marker(el)
            .setLngLat([window.js_array[i]['longitude'], window.js_array[i]['latitude']])
            .setPopup(info_window)
            .addTo(map);
        $(marker.getElement()).click(marker, function (event) {
            var marker = event.data;
            map.setCenter(marker.getLngLat());
        });
        markers.push(marker);

        var myLngLat = new goongjs.LngLat(window.js_array[i]['longitude'], window.js_array[i]['latitude']);
        bounds.extend(myLngLat);
    }
    map.fitBounds(bounds, {
        padding: {top: 100, bottom: 55, left: 25, right: 25}
    });

    $("#list_listitem li").click(function () {
        var marker = markers[$(this).attr("marker_order")];
        map.setCenter(markers[$(this).attr("marker_order")].getLngLat())
            .setZoom(18);
        var popup = marker.getPopup();
        if (popup.isOpen() === false) {
            markers[$(this).attr("marker_order")].togglePopup();
        }
    });

    function showSearchStore(array, reset = false) {
        var first_clicked = false;
        window.newarray = array;
        window.count_store = 0;
        if (array.nameCity == null) {
            array.nameCity = '';
        }
        $('span#total-stores-found-location').text(array.nameCity);
        $('#list_listitem li').each(function (i) {
            var self = this;
            $(self).attr("style", "display: none");
            markers[i].remove();
            $.each(array, function () {
                if ($(self).attr('item-id') === this['source_code']) {
                    window.count_store++;
                    $(self).attr("style", "display: block");
                    markers[$(self).attr('marker_order')].addTo(map);
                    if (first_clicked === false) {
                        if (reset) {
                            map.setCenter({lng: 107.14364350000005, lat: 15.188637531525615},).setZoom(4.669076801998534);
                        } else {
                            $(self).click();
                        }
                        first_clicked = true;
                    }
                }
            })
        })

        $('span#total-stores-found').text(window.count_store);

        if (!window.count_store) {
            $('.no-results-text').css('display', 'block');
        }
    }

    function getContentOfInfoWindow(locationId, title, description, url, address, telephone = null) {
        description = description || null;
        url = url || null;
        address = address || null;
        var content = '';

        content += '<div class="infoWindow">' +
            '<section class="tabs"><div class="tab-title">' +
            '<input id="tab-1" type="radio" name="radio-set" class="tab-selector-1" checked="checked" />' +
            '<label for="tab-1" class="tab-label-1">Address</label>';

        content += '</div>' +
            '<div class="content">' + //begin content
            '<div class="content-1">' + //begin content-1
            '<h3 name="map_info_title" id="firstHeading">' + title + '</h3>';
        if (address != null) {
            content +=
                '<div id="address"><strong>Address</strong>: ' + address + '</div>';
        }

        content += '<div class="bodyContent">'; //begin body content

        if (url != null) {
            content += '<strong><a class="viewLocationPage btn corePrettyStyle" target="_blank" href="' + url + '">View on Google Maps</a></strong>';
        }

        //begin info content
        content += '<div class="infowindowContent">';
        // if (description != null) {
        //     content += '<div id="description">' + description + '</div>';
        // }

        if (telephone != null) {
            content += '<div id="telephone"><strong>Telephone</strong>: ' + telephone + '</div>';
        }

        content += '</div>';    //end info content
        content += '</div>';    //end body content
        content += '</div>' + //end content-1
            '<div class="content-2" style="display:none" >' + //begin content-2
            '<h3 name="map_info_title" id="firstHeading">' + title + '</h3>';

        content += '</div></div></section></div>';

        return content;
    }

    function getWeekDay() {
        var weekdays = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];
        var day = new Date().getDay();

        return weekdays[day];
    }

    $("#city").change(function (e) {
        var options = [];
        var district = document.getElementById("district_id");
        $("#district_id").children('option:not(:first)').remove();
        if ($("#city").val() !== "") {
            $.each(window.districtData, function (index, value) {
                if (value.city_id == $("#city").val()) {
                    var option = document.createElement('option');
                    option.value = value.value;
                    option.text = value.full_name;
                    district.add(option);
                }
            })
        }
        findSource();

        e.preventDefault();

    });

    $("#district_id").change(function (e) {
        findSource();
        e.preventDefault();
    });

    // find all source with filter by city or district_id and render to list source
    function findSource() {
        const regexGetNumber = /(\d+)/;
        let filterInfo = $("#store-form-search").serialize();
        let fields = filterInfo.split('&');
        let cityId = (fields[1] && fields[1].match(regexGetNumber)) ? fields[1].match(regexGetNumber)[0] : null;
        let districtId = (fields[2] && fields[2].match(regexGetNumber)) ? fields[2].match(regexGetNumber)[0] : null;

        if (!cityId && !districtId) {
            showSearchStore(window.js_array, true);
        } else {
            let filterDataSources = [];
            if (!districtId) {
                filterDataSources = $.map(window.js_array, function (source, key) {
                    if(source.city_id === cityId) return source;
                });
            } else {
                filterDataSources = $.map(window.js_array, function (source, key) {
                    if(source.city_id === cityId && source.district_id === districtId) return source;
                });
            }

            showSearchStore(filterDataSources);
        }
    }
});
