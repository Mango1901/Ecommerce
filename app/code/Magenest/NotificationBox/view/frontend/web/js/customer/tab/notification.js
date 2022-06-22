define(
    [
        'jquery',
        'underscore',
        'uiComponent',
    ],
    function ($, _, Component) {
        'use strict';
        return function(config){
            var self = this;
            var urlDelete           = config.urlDelete;
            var urlMarkAsRead       = config.urlMarkAsRead;
            var totalNotification   = config.totalNotification;
            var totalSelected = $('#notification-selected').val();
            var urlBase = config.baseUrl;
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);
            var typeSelectNotificationBox = urlParams.get('type');
            if(typeSelectNotificationBox == null){
                $("#filter_notification").val('select-all');
            }else{
                $("#filter_notification").val(typeSelectNotificationBox);
            }
            var vars = {};
            var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                vars[key] = value;
            });
            $(document).ready(function() {

                $(".category-notification-btn").on('click touch', function (){
                    var id = this.id;
                    var notification_type_id = id.replace('filter-','');
                    if($("#"+id).hasClass('is_filter')){
                        window.location.href = urlBase + "notibox/customer/notification";
                    }
                    else{
                        window.location.href = urlBase + "notibox/customer/notification?type="+notification_type_id;
                    }
                    return false;
                })
            });


        // delete notification
            $('.icon-remove').on('click touch', function (){
                var totalSelected =0;
                var listNotificationSelected = [];
                $('#my-notification-table > tbody  > tr > td > label > input').each(function(index, input) {
                    if(input.checked == true){
                        totalSelected ++;
                        listNotificationSelected.push(input.classList[1]);
                    }
                });
                if(totalSelected >0)
                {
                    var deleteConfirm = confirm("Do you want to delete notifications?");
                    if (deleteConfirm == true) {

                        $.ajax({
                            method: "GET",
                            dataType: 'json',
                            showLoader: true,
                            url: urlDelete,
                            data: {
                                type:"delete",
                                listNotificationSelected:listNotificationSelected
                            },
                            success:(function () {
                                location.reload();
                            })
                        });
                    }
                }


            })
        //Mark as read
            $('.mark-as-read-notification').on('click touch', function (){
                var totalSelected =0;
                var listNotificationSelected = [];
                $('#my-notification-table > tbody  > tr > td > label > input').each(function(index, td) {
                    if(td.checked == true){
                        totalSelected ++;
                        listNotificationSelected.push(td.classList[1]);
                    }
                });
                if(totalSelected >0)
                {
                    $.ajax({
                        method: "GET",
                        dataType: 'json',
                        showLoader: true,
                        url: urlDelete,
                        data: {
                            type:'maskAsRead',
                            listNotificationSelected:listNotificationSelected
                        },
                        success:(function (response) {
                            location.reload();
                        })
                    });
                }
            })

            //Mark as read
            $('.unstar-notification').on('click touch', function (){
                var totalSelected =0;
                var listNotificationSelected = [];
                $('#my-notification-table > tbody  > tr > td > label > input').each(function(index, td) {
                    if(td.checked == true){
                        totalSelected ++;
                        listNotificationSelected.push(td.className);
                    }
                });
                if(totalSelected >0)
                {
                    $.ajax({
                        method: "GET",
                        dataType: 'json',
                        showLoader: true,
                        url: urlDelete,
                        data: {
                            type:'unstar',
                            listNotificationSelected:listNotificationSelected
                        },
                        success:(function (response) {
                            location.reload();
                        })
                    });
                }
            })



            $('#select-all-notification > a').on('click touch', function (){
                $("#my-notification-table tr :checkbox").prop('checked', true);
                $('#notification-selected').text(totalNotification);
                $('#select-all-notification').hide();
            });

            //handle when select or unselect all
            $('#select_notification').on('change', function (e) {
                var optionSelected = $("option:selected", this);
                var valueSelected = this.value;
                var totalSelected = 0;

                if(valueSelected === 'select-all' || valueSelected === 'select-none'){
                    hideSelectAll();
                    var select = true;
                    if(valueSelected === 'select-none'){
                        select = false;
                    }
                    $("#my-notification-table tr :checkbox").prop('checked', select);
                    $('#my-notification-table > tbody  > tr > td > label > input').each(function(index, td) {
                        if(td.checked == true){
                            totalSelected ++;
                        }
                    });

                    $('#notification-selected').text(totalSelected);
                }

                if(valueSelected === 'select-read'){
                    $('#my-notification-table > tbody  > tr > td > label > input').each(function(index, td) {
                        if(td.id !== 'undefined' &&   td.id !== '')
                        {
                            td.checked = false;
                        }
                        else{
                            td.checked = true;
                            totalSelected ++;
                        }
                    });
                    showHideSelectAll(totalSelected)
                }
                if(valueSelected === 'select-unread'){
                    $('#my-notification-table > tbody  > tr > td > label > input').each(function(index, td) {
                        if(td.id !== 'undefined' &&   td.id !== '')
                            {
                                td.checked = true;
                                totalSelected ++;
                            }
                            else{
                                td.checked = false;
                            }
                    });
                    showHideSelectAll(totalSelected)
                }

                if(valueSelected === 'select-important'){
                    $('#my-notification-table > tbody  > tr > td > label > input').each(function(index, element) {
                        var listClass = element.parentElement.parentElement.parentElement.children[1].lastElementChild.className;
                        if(listClass.indexOf('active') == -1){
                            element.checked = false;
                        }
                        else {
                            element.checked = true;
                            totalSelected++;
                        }
                    });
                    showHideSelectAll(totalSelected)
                }
                if(valueSelected === 'select-unimportant'){
                    $('#my-notification-table > tbody  > tr > td > label > input').each(function(index, element) {
                        var listClass = element.parentElement.parentElement.parentElement.children[1].lastElementChild.className;
                        if(listClass.indexOf('active') == -1){
                            element.checked = true;
                            totalSelected++;
                        }
                        else {
                            element.checked =  false;
                        }
                    });
                    showHideSelectAll(totalSelected)
                }

            });
            function showHideSelectAll(totalSelected){

                $('#notification-selected').text(totalSelected);
                if(totalSelected < totalNotification && totalSelected !== 0){
                    showSelectAll();
                }else{
                    hideSelectAll();
                }
            }

            function hideSelectAll(){
                $('#select-all-notification').hide();
            }

            function showSelectAll(){
                $('#select-all-notification').show();
            }

            function changeBackgroundColorNotification(element){
                $('#select-all-notification').style.backgroundColor = "red";
            }

            $("#delete-all").on("click", function () {
                $.ajax({
                    method: "GET",
                    dataType: 'json',
                    showLoader: true,
                    url: urlDelete,
                    data: {},
                    success: $.proxy(function (response) {
                        location.reload();
                    })
                });
            });
            $("#mark-all-as-read").on("click", function () {
                $.ajax({
                    method: "GET",
                    dataType: 'json',
                    showLoader: true,
                    url: urlMarkAsRead,
                    data: {},
                    success: $.proxy(function (response) {
                        location.reload();
                    })
                });
            });

            //handle when select notification #d8e9ff
            $('.checkbox-notification-input').on('click',function (){
                var totalSelected = $('#notification-selected').val();
                $('#my-notification-table > tbody  > tr > td > label > input').each(function(index, input) {
                    if(input.checked == true){
                        totalSelected ++;
                    }
                });
                $('#notification-selected').text(totalSelected);
                if(totalSelected < totalNotification){
                    showSelectAll();
                }
                else{
                    hideSelectAll();
                }
                if(totalSelected == 0){
                    $('#notification-selected').text(0);
                    hideSelectAll();
                }
            })

            $(".marking-important").on('touch click',function (){
                var important;
                var id = $(this).parent().parent().attr('id');
                $(this).toggleClass('active');
                if($(this).hasClass('active')){
                    important = 1;
                }
                else{
                    important = 0;
                }
                var url = config.urlMarkImportant;
                $.ajax({
                    method: "POST",
                    dataType: 'json',
                    url: url,
                    data: {
                        id:id,
                        important:important
                    }
                })
            })
            //handle when click to notification on grid
            $(".notification-image ,.notification-type, .description, .created-at").on('touch click', function () {
                var id = $(this).parent().attr('id');
                window.location.href = config.urlViewNotification + '?id=' + id;
            })
        }
    },
);
