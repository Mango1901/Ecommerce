/**
 * Created by hiennq on 9/15/16.
 */

require([
    'jquery'
], function ($) {
    var array = window.js_array;
    var max_item = parseInt(window.max_item);

    $("#ListContainer .li-location").click(function () {
        $("#ListContainer .li-location").removeClass("selected");
        $(this).addClass("selected");
    });

    $('#location_sort').on("change", function () {
        if ($(this).val() === 'name') {
            array.sort(function (a, b) {
                return b['title'] - a['title'];
            });
        }
    });
    var cur_page = parseInt($('#list_listitem').attr("cur-page"));
    var begin_index = 0;
    $('#btn_prev').on("click", function () {
        array = getArray();
        begin_index -= max_item;
        cur_page--;
        if (begin_index < 0) {
            begin_index = 0;
            cur_page = 1;
        }
        performPaging(array, begin_index, cur_page);
    });
    $('#btn_next').on("click", function () {
        array = getArray();
        begin_index += max_item;
        cur_page++;
        if (begin_index >= array.length) {
            begin_index -= max_item;
            cur_page--;
        }
        performPaging(array, begin_index, cur_page);
    });

    $('#btn_reset').on("click", function () {
        array = getArray();
        begin_index = 0;
        cur_page = 1;
        performPaging(array, begin_index, cur_page);
    });

    // $("#filter").hide();
    // $("#store-advanced-search").click(function () {
    //    $("#filter").slideToggle();
    // });

    function getArray() {
        if ((typeof window.newarray == 'undefined')) {
            array = window.js_array;
        } else {
            array = window.newarray;
        }
        return array;
    }
});

function performPaging(array, begin_index, cur_page) {
    require([
        'jquery',
        'mage/url'
    ], function ($, url) {
        var index = parseInt(begin_index);
        $('#list_listitem').attr("cur-page", cur_page);
        $('#cur_page').text(cur_page);
        $('#list_listitem li').each(function (i) {
            if (index < array.length) {
                $(this).attr("style", "");
                $(this).attr("item-id", array[index]['location_id']);
                $(this).attr("marker_order", index);
                $(this).find('.locationTitle').text(array[index]['title'] === null ? "" : array[index]['title']);
                $(this).find('.locationTitle').attr("href", url.build('maplist/view/index') + "/id/" + array[index]['map_location_id']);
                $(this).find('.viewLocationPage').attr("href", url.build('maplist/view/index') + "/id/" + array[index]['location_id']);
                $(this).find('.mapDescriptionContent').text(array[index]['short_description'] === null ? "" : array[index]['short_description']);
                $(this).find('.mapAddressContent').text(array[index]['address'] === null ? "" : array[index]['address']);
                $(this).find('.mapWebsiteContent').text(array[index]['mapWebsite'] === null ? "" : array[index]['mapWebsite']);
                $(this).find('.mapPhoneContent').text(array[index]['phone_number'] === null ? "" : array[index]['phone_number']);
                $(this).find('.mapEmailContent').text(array[index]['email'] === null ? "" : array[index]['email']);
            } else {
                $(this).attr("style", "display: none");
            }
            index++;
        })
    });
}
