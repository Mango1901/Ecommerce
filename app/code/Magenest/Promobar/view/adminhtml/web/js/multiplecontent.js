define([
    "jquery",
    "ko",
    "uiClass",
    "Magento_Ui/js/modal/modal",
    "underscore",
    "validation"
], function ($, ko, Class) {

    function TemplateMultipleContent(dataRow, id) {
        var self = this;
        if (dataRow.content !== "") {
            var content = JSON.parse(dataRow.content);
            self.content_text = ko.observable((content.content));
        } else {
            self.content_text = ko.observable("");
        }
        if (dataRow.button !== "" && isJson(dataRow.button)) {
            var button = JSON.parse(dataRow.button);
            if (button !== null) {
                if (button.button) {
                    self.button_title = ko.observable((button.button.data.title.replace(/<[^>]+>/g, '')));
                } else {
                    self.button_title = ko.observable("");
                }
            }else{
                self.button_title = ko.observable("");
            }
        } else {
            self.button_title = ko.observable("");
        }
        self.content = ko.observable(dataRow.content);
        self.button = ko.observable(dataRow.button);
        self.id = ko.observable(id);
    }

    function TemplateModel(config) {
        var self = this;
        self.templateMultipleContent = ko.observableArray([]);
        var templateData = config.mapmultiplecontent;
        var mobileTemplateData = config.mapmobilemultiplecontent;
        var id = -1;
        var map = $.map(templateData, function (data) {
            id++;
            if (id === 0) {
                renderPreviewBar(data);
                showInfoBar(data);
            }
            return new TemplateMultipleContent(data, id);
        });
        self.templateMultipleContent(map);
        if (id === -1) {
            self.templateMultipleContent.push(new TemplateMultipleContent({
                content: "",
                button: ""
            }, 0));
        }

        self.valueMultipleContent = function () {

            var optionIds = $.map(self.templateMultipleContent(), function (template) {
                return template.id();
            });
            var maxId;
            if (optionIds !== '' || optionIds.length > 0) {
                maxId = Math.max.apply(this, optionIds);
                maxId;
            }
            // $(".add-option")[0].setAttribute("id", maxId);
            document.getElementsByClassName("add-option")[0].setAttribute("id", maxId);
            if (maxId > 0) {
                $('.preview-slider').css('display', 'block');
            }
        };

        self.addMultipleContent = function () {

            var optionIds = $.map(self.templateMultipleContent(), function (template) {
                return template.id();
            });
            var maxId =0;
            if (optionIds !== '' || optionIds.length > 0) {
                maxId = Math.max.apply(this, optionIds);
                maxId++;
            }
            self.templateMultipleContent.push(new TemplateMultipleContent({
                content: "",
                button: ""
            }, maxId));
            // $(".add-option")[0].setAttribute("id", maxId);
            document.getElementsByClassName("add-option")[0].setAttribute("id", maxId);
        };
        self.deleteMultipleContent = function (templateMultipleContent) {
            ko.utils.arrayForEach(self.templateMultipleContent(), function (template) {
                if (templateMultipleContent.id() === template.id()) {
                    document.getElementsByClassName("action-delete")[0].setAttribute("id", template.id());
                    self.templateMultipleContent.destroy(template);
                }
            });
        };
        self.clickEdit = function (templateMultipleContent) {
            var row = $('.add-option').attr('id');

            //clear css and text row old
            while (row >= 0) {
                id = "#select-" + row;
                $(id).text("Select");
                $('.action-select').css('color', '#41362f');
                $('tr#' + row).css('border', '');
                row--;
            }

            //update new css and text for new row click
            var idRow = templateMultipleContent.id();
            var current_id = "#select-" + idRow;
            $(current_id).text("Edit");
            $('.action-select-wrap').removeClass(" _active");
            $('.action-menu').removeClass(" _active");
            $(current_id).css('color', '#007bdb');
            $('tr#' + idRow).css('border', '2px solid #007bdb');
        };
        self.clickSelect = function () {

        }
    }

    function renderPreviewBar(data) {
        //render content promo bar
        var dataContent = [];
        var dataButton = [];
        if (isJson(data.content)) {
            dataContent = JSON.parse(data.content);
        }
        if (isJson(data.button)) {
            dataButton = JSON.parse(data.button);
        }
        var mobileDataContent = JSON.parse(data.mobile_content);
        var mobileDataButton = JSON.parse(data.mobile_button);
        $(".promoItem > div").css("background-color", dataContent['backgroundColor']);
        $(".mobile-promoItem > div").css("background-color", dataContent['backgroundColor']);
        $('.promoItemContent > div').css('color', dataContent['textColor']);
        $(".promoItemContent > div").html(dataContent['content']);
        $('.mobile-promoItemContent > div').css('color', dataContent['textColor']);
        $(".mobile-promoItemContent > div").html(dataContent['content']);
        $('.promoItemButton a').attr('href', dataContent['url']);
        $('.mobile-promoItemButton a').attr('href', dataContent['url']);
        $('.promoItemContent > div').css('font-size', dataContent['size'] + 'px');
        $('.promoItemContainer').css('top', dataContent['positionText']);
        $('.mobile-promoItemContent > div').css('font-size', dataContent['mobile_text_size'] + 'px');
        $('.mobile-promoItemContainer').css('top', dataContent['mobilePositionText']);
        //render button
        if (dataButton['button']) {
            $('.mobile-promoItemButton a').css('background-color', mobileDataButton['button']['data']['background_color']);
            $('.promoItemButton').show();
            $('.mobile-promoItemButton').show();
            $(".promoItemButton a").css({
                "display": "inline-block",
                "top": dataButton['button']['data']['upDown'],
                'height': dataButton['button']['edit_button']['height'] + 'px',
                'width': dataButton['button']['edit_button']['width'] + 'px',
                'font-size': dataButton['button']['data']['size'] + 'px',
                'border': dataButton['button']['data']['border_style']
                    + ' ' + dataButton['button']['data']['border_width']
                    + 'px ' + dataButton['button']['data']['background_color_border']
            });
            $(".mobile-promoItemButton a").css({
                "display": "inline-block",
                "top": dataButton['button']['data']['mobileUpDown'],
                'height': dataButton['button']['edit_button']['height'] + 'px',
                'width': dataButton['button']['edit_button']['width'] + 'px',
                'font-size': dataButton['button']['data']['size'] + 'px',
                'border': dataButton['button']['data']['border_style']
                    + ' ' + dataButton['button']['data']['border_width']
                    + 'px ' + dataButton['button']['data']['background_color_border']
            });
            // $(".promoItemButton a").css("display", "inline-block");
            // $('.promoItemButton a').css('width', dataButton['button']['edit_button']['width'] + 'px');
            // $('.promoItemButton a').css('font-size', dataButton['button']['data']['size'] + 'px');
            // $('.promoItemButton a').css('height', dataButton['button']['edit_button']['height'] + 'px');
            // $('.promoItemButton a').css('border',dataButton['button']['data']['border_style']+' '+dataButton['button']['data']['border_width']+'px '+dataButton['button']['data']['background_color_border']);
            $('.promoItemButton').css('width', dataButton['button']['edit_button']['width'] + 'px');
            $('.mobile-promoItemButton').css('width', dataButton['button']['edit_button']['width'] + 'px');
            $('div.dv-content-button').html(dataButton['button']['data']['content']);
            $('div.mobile-dv-content-button').html(dataButton['button']['data']['content']);
            if ((dataButton['button']['edit_button']['top_left'] + dataButton['button']['edit_button']['top_right'] + dataButton['button']['edit_button']['bottom_right'] + dataButton['button']['edit_button']['bottom_left']) > 0) {
                var border = dataButton['button']['edit_button']['top_left'] + 'px' + ' ' + dataButton['button']['edit_button']['top_right'] + 'px' + ' ' + dataButton['button']['edit_button']['bottom_right'] + 'px' + ' ' + dataButton['button']['edit_button']['bottom_left'] + 'px';
                $('.promoItemButton a').css('border-radius', border);
                $('.mobile-promoItemButton a').css('border-radius', border);
            } else {
                $('.promoItemButton a').css('border-radius', dataButton['button']['edit_button']['border'] + 'px');
                $('.mobile-promoItemButton a').css('border-radius', mobileDataButton['button']['edit_button']['border'] + 'px');
            }

            //padding text of button
            if (dataButton['button']['edit_button']['padding_right']) {
                $("div.dv-content-button").css("right", "0");
                $("div.dv-content-button").css("padding-right", dataButton['button']['edit_button']['padding_right'] + "px");
                $("div.mobile-dv-content-button").css("right", "0");
                $("div.mobile-dv-content-button").css("padding-right", dataButton['button']['edit_button']['padding_right'] + "px");
            }
            if (dataButton['button']['edit_button']['padding_left']) {
                $("div.dv-content-button").css("left", "0");
                $("div.dv-content-button").css("padding-left", dataButton['button']['edit_button']['padding_left'] + "px");
                $("div.mobile-dv-content-button").css("left", "0");
                $("div.mobile-dv-content-button").css("padding-left", dataButton['button']['edit_button']['padding_left'] + "px");
            }
            if (dataButton['button']['edit_button']['padding_top']) {
                $("div.dv-content-button").css("top", "0");
                $("div.dv-content-button").css("padding-top", dataButton['button']['edit_button']['padding_top'] + "px");
                $("div.mobile-dv-content-button").css("top", "0");
                $("div.mobile-dv-content-button").css("padding-top", dataButton['button']['edit_button']['padding_top'] + "px");
            }
            if (dataButton['button']['edit_button']['padding_bottom']) {
                $("div.dv-content-button").css("bottom", "0");
                $("div.dv-content-button").css("padding-bottom", dataButton['button']['edit_button']['padding_bottom'] + "px");
                $("div.mobile-dv-content-button").css("bottom", "0");
                $("div.mobile-dv-content-button").css("padding-bottom", dataButton['button']['edit_button']['padding_bottom'] + "px");
            }


            //position button
            if (dataButton['button']['data']['displayLeft'] !== 'no check') {
                $('.promoItemContainer').css('direction', 'rtl');
                $('.promoItemContainer').css('margin-left', dataButton['button']['data']['displayLeft']);
                $('.promoItemContainer').css('width', 'calc(100% - ' + dataButton['button']['data']['displayLeft'] + ')');
            }
            if (dataButton['button']['data']['displayRight'] !== 'no check') {
                $('.promoItemContainer').css('direction', 'ltr');
                $('.promoItemContainer').css('margin-right', dataButton['button']['data']['displayRight']);
                $('.promoItemContainer').css('width', 'calc(100% - ' + dataButton['button']['data']['displayRight'] + ')');

            }

            // mobile position button
            if (mobileDataButton['button']['data']['displayLeft'] !== 'no check') {
                $('.mobile-promoItemContainer').css('direction', 'rtl');
                $('.mobile-promoItemContainer').css('margin-left', mobileDataButton['button']['data']['displayLeft']);
                $('.mobile-promoItemContainer').css('width', 'calc(100% - ' + mobileDataButton['button']['data']['displayLeft'] + ')');
            }

            if (mobileDataButton['button']['data']['displayRight'] !== 'no check') {
                $('.mobile-promoItemContainer').css('direction', 'ltr');
                $('.mobile-promoItemContainer').css('margin-right', mobileDataButton['button']['data']['displayRight']);
                $('.mobile-promoItemContainer').css('width', 'calc(100% - ' + mobileDataButton['button']['data']['displayRight'] + ')');
            }


            $('.promoItemButton a').css('top', dataButton['button']['data']['upDown']);
            $('.mobile-promoItemButton a').css('top', mobileDataButton['button']['data']['mobile_upDown']);


            //color text
            $('div.dv-content-button').css("color", dataButton['button']['data']['text_color']).val(dataButton['button']['data']['text_color']);
            $('div.mobile-dv-content-button').css("color", dataButton['button']['data']['text_color']).val(dataButton['button']['data']['text_color']);
            //hover color text button
            $(".promoItemButton a").hover(function () {
                if (dataButton['button']['data']['hover_color_text'] === null) {
                    $('div.dv-content-button').css("color", dataButton['button']['data']['text_color']).val(dataButton['button']['data']['text_color']);
                } else {
                    $('div.dv-content-button').css("color", dataButton['button']['data']['hover_color_text']).val(dataButton['button']['data']['hover_color_text']);
                }
            }, function () {
                $('div.dv-content-button').css("color", dataButton['button']['data']['text_color']).val(dataButton['button']['data']['text_color']);
            });

            $(".mobile-promoItemButton a").hover(function () {
                if (dataButton['button']['data']['hover_color_text'] === null) {
                    $('div.mobile-dv-content-button').css("color", dataButton['button']['data']['text_color']).val(dataButton['button']['data']['text_color']);
                } else {
                    $('div.mobile-dv-content-button').css("color", dataButton['button']['data']['hover_color_text']).val(dataButton['button']['data']['hover_color_text']);
                }
            }, function () {
                $('div.mobile-dv-content-button').css("color", dataButton['button']['data']['text_color']).val(dataButton['button']['data']['text_color']);
            });


            //background color

            if (dataButton['button']['data']['background_color'] === null) {
                $(".promoItemButton a").css("background-color", "transparent");
            } else {
                $(".promoItemButton a").css("background-color", dataButton['button']['data']['background_color']).val(dataButton['button']['data']['background_color']);
            }
            //hover color background button
            $(".promoItemButton a").hover(function () {
                if (dataButton['button']['data']['hover_color_button'] === null) {
                    if (dataButton['button']['data']['background_color_border'] === null) {
                        $(".promoItemButton a").css("background-color", "transparent");
                    } else {
                        $(".promoItemButton a").css("background-color", dataButton['button']['data']['background_color_border']).val(dataButton['button']['data']['background_color_border']);
                    }
                } else {
                    $(this).css("background-color", dataButton['button']['data']['hover_color_button']).val(dataButton['button']['data']['hover_color_button']);
                }
            }, function () {
                if (dataButton['button']['data']['background_color'] === null) {
                    $(this).css("background-color", "transparent");
                } else {
                    $(this).css("background-color", dataButton['button']['data']['background_color']).val(dataButton['button']['data']['background_color']);
                }
            });

            $(".mobile-promoItemButton a").hover(function () {
                if (dataButton['button']['data']['hover_color_button'] === null) {
                    if (dataButton['button']['data']['background_color_border'] === null) {
                        $(".mobile-promoItemButton a").css("background-color", "transparent");
                    } else {
                        $(".mobile-promoItemButton a").css("background-color", dataButton['button']['data']['background_color_border']).val(dataButton['button']['data']['background_color_border']);
                    }
                } else {
                    $(this).css("background-color", dataButton['button']['data']['hover_color_button']).val(dataButton['button']['data']['hover_color_button']);
                }
            }, function () {
                if (dataButton['button']['data']['background_color'] === null) {
                    $(this).css("background-color", "transparent");
                } else {
                    $(this).css("background-color", dataButton['button']['data']['background_color']).val(dataButton['button']['data']['background_color']);
                }
            });


            if (dataButton['button']['data']['background_color_border'] === null) {
                $(".promoItemButton a").css("border-color", "transparent");
                $(".mobile-promoItemButton a").css("border-color", "transparent");
            } else {
                $(".promoItemButton a").css("border-color", dataButton['button']['data']['background_color_border']).val(dataButton['button']['data']['background_color_border']);
                $(".mobile-promoItemButton a").css("border-color", dataButton['button']['data']['background_color_border']).val(dataButton['button']['data']['background_color_border']);
            }
            //hover color border button
            $(".promoItemButton a").hover(function () {
                if (dataButton['button']['data']['hover_color_border'] === null) {
                    if (dataButton['button']['data']['background_color_border'] === null) {
                        $(this).css("border-color", "transparent");
                    } else {
                        $(this).css("border-color", dataButton['button']['data']['background_color_border']).val(dataButton['button']['data']['background_color_border']);
                    }
                } else {
                    $(this).css("border-color", dataButton['button']['data']['hover_color_border']).val(dataButton['button']['data']['hover_color_border']);
                }
            }, function () {
                if (dataButton['button']['data']['background_color_border'] === null) {
                    $(this).css("border-color", "transparent");
                } else {
                    $(this).css("border-color", dataButton['button']['data']['background_color_border']).val(dataButton['button']['data']['background_color_border']);
                }
            });

        } else {
            $('.promoItemButton').hide();
        }

    };

    function showInfoBar(data) {
        var dataContent = [];
        var dataButton = [];
        if (isJson(data.content)) {
            dataContent = JSON.parse(data.content);
        }
        if (isJson(data.button)) {
            dataButton = JSON.parse(data.button);
        }
        // var dataButton = JSON.parse(data.button);
        var mobileDataContent = JSON.parse(data.mobile_content);
        var mobileDataButton = JSON.parse(data.mobile_button);
        var positionButton;
        var mobilePositionButton;
        $("#background_color").css("background-color", dataContent['backgroundColor']);
        $("#background_text").css("background-color", dataContent['textColor']);
        $("#background_color").val(dataContent['backgroundColor']);
        $("#background_text").val(dataContent['textColor']);

        if (dataButton['button']) {
            $("#button_id option").each(function () {
                if ($(this).html() == dataButton['button']['data']['title']) $(this).attr("selected", "selected");
            });
            $('#check-range-right').removeAttr("disabled");
            $('#check-range-left').removeAttr("disabled");
            $('#mobile-check-range-right').removeAttr("disabled");
            $('#mobile-check-range-left').removeAttr("disabled");
        } else {
            $("#button_id option[value=0]").attr('selected', 'selected');
            $('#check-range-right').attr("disabled", true);
            $('#check-range-left').attr("disabled", true);
            $('#mobile-check-range-right').attr("disabled", true);
            $('#mobile-check-range-left').attr("disabled", true);
        }
        $("#content").val(dataContent['content']);
        $("#content_ifr").contents().find('body').html(dataContent['content']);
        $("#size").val(dataContent['size']);
        $("#url").val(dataContent['url']);
        if (dataContent['positionText'] !== undefined) {
            var positionText = dataContent['positionText'].replace('px', '');
            $('#desktop-input-range-position-text').attr('value', positionText);
            $('.range-value-position-text').html(dataContent['positionText']);
        }
        if (dataButton['button']) {
            if (dataButton['button']['data']['displayLeft'] !== 'no check') {
                $('#check-range-left').prop('checked', true);
                $('.input-range-left').css('display', 'block');
                $('.range-value-left').css('display', 'block');
                positionButton = dataButton['button']['data']['displayLeft'].replace('%', '');
                $('.input-range-left').attr('value', positionButton);
                $('.range-value-left').html(dataButton['button']['data']['displayLeft']);
            }
            if (dataButton['button']['data']['displayRight'] !== 'no check') {
                $('#check-range-right').prop('checked', true);
                $('.input-range-right').css('display', 'block');
                $('.range-value-right').css('display', 'block');
                positionButton = dataButton['button']['data']['displayRight'].replace('%', '');
                $('.input-range-right').attr('value', positionButton);
                $('.range-value-right').html(dataButton['button']['data']['displayRight']);
            }
            if (mobileDataButton['button']['data']['displayLeft'] !== 'no check') {
                $('#mobile-check-range-left').prop('checked', true);
                $('.mobile-input-range-left').css('display', 'block');
                $('#mobile-range-value-left').css('display', 'block');
                mobilePositionButton = mobileDataButton['button']['data']['displayLeft'].replace('%', '');
                $('.mobile-input-range-left').attr('value', mobilePositionButton);
                $('#mobile-range-value-left').html(mobileDataButton['button']['data']['displayLeft']);
            }
            if (mobileDataButton['button']['data']['displayRight'] !== 'no check') {
                $('#mobile-check-range-right').prop('checked', true);
                $('.mobile-input-range-right').css('display', 'block');
                $('#mobile-range-value-right').css('display', 'block');
                mobilePositionButton = mobileDataButton['button']['data']['displayRight'].replace('%', '');
                $('.mobile-input-range-right').attr('value', mobilePositionButton);
                $('#mobile-range-value-right').html(mobileDataButton['button']['data']['displayRight']);
            }
            positionButton = dataButton['button']['data']['upDown'].replace('px', '');
            mobilePositionButton = mobileDataButton['button']['data']['mobileUpDown'].replace('px', '');

            $('.input-range-button-up-down').attr('value', positionButton);
            $('.range-value-button-up-down').html(dataButton['button']['data']['upDown']);

            $('#mobile-input-range-button-up-down').attr('value', mobilePositionButton);
            $('#mobile-range-value-button-up-down').html(mobileDataButton['button']['data']['mobileUpDown']);
        }
    }

    function isVisible(row, container) {

        var elementTop = $(row).offset().top,
            elementHeight = $(row).height(),
            containerTop = container.scrollTop(),
            containerHeight = container.height();
        return ((((elementTop - containerTop) + elementHeight) > 0) && ((elementTop - containerTop) < containerHeight));
    }

    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    $(window).scroll(function () {
        if (isVisible($("#add_promobars_form"), $(window)) && $(window).scrollTop() !== 0) {
            $(".mobile-section-promobar").stop().hide();
            $("#mobile_section_title").stop().hide();
            $(".section-promobar").stop().show();
            $("#desktop_section_title").stop().show()
        }
        if (isVisible($("#add_mobile_promobars_form"), $(window)) && $(window).scrollTop() !== 0) {
            $(".mobile-section-promobar").stop().show();
            $("#mobile_section_title").stop().show();
            $(".section-promobar").stop().hide();
            $("#desktop_section_title").stop().hide();
        }

        if ($(window).scrollTop() === 0) {
            $(".mobile-section-promobar").stop().show();
            $("#mobile_section_title").stop().show();
            $(".section-promobar").stop().show();
            $("#desktop_section_title").stop().show();
        }
    });

    return Class.extend({

        defaults: {
            template: "Magenest_Promobar/options"
        },
        initObservable: function () {
            this._super();
            return this;
        },
        initialize: function (config) {
            this._super();
            var self = this;
            if ($(window).scrollTop() === 0) {
                $(".mobile-section-promobar").stop().show();
                $("#mobile_section_title").stop().show();
                $(".section-promobar").stop().show();
                $("#desktop_section_title").stop().show();
            }
            this.initConfig(config);
            this.bindAction(self);
            return this;
        },
        bindAction: function (self) {
            var config = self;
            ko.cleanNode(document.getElementById("mapmultiplecontent"));
            ko.applyBindings(new TemplateModel(config), document.getElementById("mapmultiplecontent"));
        }


    });
});
