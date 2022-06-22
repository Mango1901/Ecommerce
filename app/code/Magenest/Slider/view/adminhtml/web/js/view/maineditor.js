define([
    'jquery',
    "underscore",
    'uiComponent',
    'ko',
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm',
    'Magento_Variable/variables',
    'Magento_Ui/js/modal/modal',
    'mage/collapsible',
    'jquery/ui',
    'mage/adminhtml/wysiwyg/tiny_mce/setup',
    'mage/validation'
    ], function ($, _, Component, ko, $t, alert, confirmation) {
        'use strict';

        ko.extenders.validateInput = function(target, param) {
            //add some sub-observables to our observable
            target.isInt = ko.observable(true);
            target.isEmpty = ko.observable(false);
            target.isNegaInt = ko.observable(true);
            target.hasError = ko.observable(false);
            target.validationMessage = ko.observable();

            //define a function to do validation
            function validate(newValue) {
                target.hasError(false);
                target.isEmpty(newValue === '' ? true : false);
                target.isInt(newValue !== '' && Number.isInteger(Number(newValue)) ? true : false);
                target.isNegaInt(newValue !== '' && Number(newValue) >= 0 ? true : false);

                var mess = "";
                if (target.isEmpty() === true){
                    if (param.required){
                        target.hasError(true);
                        mess += "This is required field. \n";
                    }
                } else {
                    if (param.checkNegaInt && target.isNegaInt() === false){
                        target.hasError(true);
                        mess += "You must enter a numeric value greater than 0. \n";
                    }
                    if (param.checkInt && target.isInt() === false){
                        target.hasError(true);
                        mess += "You must enter the integer value. \n";
                    }
                }

                if (target.hasError() === false){
                    jQuery('#save, #save_and_continue').attr('disabled', false);
                    target.validationMessage("");
                } else {
                    jQuery('#save, #save_and_continue').attr('disabled', true);
                    target.validationMessage(mess);
                }

            }

            //initial validation
            validate(target());

            //validate whenever the value changes
            target.subscribe(validate);

            //return the original observable
            return target;
        };

        function replaceDefaultValue(defaultValue, datas) {
            if (datas === undefined) {
                datas = defaultValue;
            } else {
                $.each(defaultValue, function (key, value) {
                    if (datas[key] === undefined || datas[key] === ''){
                        datas[key] = value;
                    }
                });

            }
            return datas;
        }

        function Slider(dataSlider, config) {
            var self = this;

            var defaultItems = ko.toJSON(ko.observableArray([new Item(undefined, config)]));
            var defaultValue = {
                customClass: '',
                numOfItemsShow: 1,
                sliderAnimate: 0,
                sliderAnimateOut: 0,
                sliderMargin: 0,
                sliderArrows: true,
                sliderDots: 0,
                autoHeight: 0,
                sliderLoop: 0,
                autoPlay: 0,
                autoPlaySpeed: 0,
                pauseHover: 0,
                draggable: 1,
                centerMode: 0,
                customCss: '',
                items: JSON.parse(defaultItems)
            }

            dataSlider = replaceDefaultValue(defaultValue, dataSlider); // if the attibute undefined => replace with default value

            self.customClass = ko.observable(dataSlider.customClass);
            self.numOfItemsShow = ko.observable(dataSlider.numOfItemsShow);
            self.optionChooseNumOfItemShow = ko.observableArray()
            self.sliderAnimate = ko.observable(dataSlider.sliderAnimate);
            self.sliderAnimateOut = ko.observable(dataSlider.sliderAnimateOut);
            self.sliderMargin = ko.observable(dataSlider.sliderMargin).extend({ validateInput: {required: true, checkInt: false, checkNegaInt: true} });
            self.sliderArrows = ko.observable(dataSlider.sliderArrows);
            self.sliderDots = ko.observable(dataSlider.sliderDots);
            self.autoHeight = ko.observable(dataSlider.autoHeight);
            self.sliderLoop = ko.observable(dataSlider.sliderLoop);
            self.autoPlay = ko.observable(dataSlider.autoPlay);
            self.autoPlaySpeed = ko.observable(dataSlider.autoPlaySpeed).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: true} });
            self.pauseHover = ko.observable(dataSlider.pauseHover);
            self.draggable = ko.observable(dataSlider.draggable);
            self.centerMode = ko.observable(dataSlider.centerMode);
            self.customCss = ko.observable(dataSlider.customCss);

            var items = [];
            $.each(dataSlider.items, function (index, item) {
                items.push(new Item(item, config));
            });

            self.items = ko.observableArray(items);

            self.currentItem = ko.observable(self.items()[0]);

            self.addItem = function () {
                var newID = self.items().length + 1;
                self.items.push(
                    new Item({
                        id: 0,
                        orderId: newID,
                        itemName: 'Slider Item ' + newID
                    }, config)
                );
                self.currentItem(self.items()[self.items().length - 1]);
            }

            this.addItemSliderSyncing = function (data) {
                // add item when type of slider is slider syncing
                data.slider.addItem();
                data.childSlider.addItem();
            };

            self.removeItem = function (item) {
                self.items.remove(item);
                var orderId = 0;
                $.each(self.items(), function (index, value) {
                    self.items()[index].orderId(orderId);
                    orderId++;
                    self.items()[index].itemName('Slider Item ' + orderId);
                });
            };

            self.switchItem = function (data, event, item) {
                var indexItem = self.items.indexOf(item);
                self.currentItem(item);
            }

            self.rmCurrentItem = function (data, event, item) {
                var indexItem = self.items.indexOf(item());
                self.removeItem(item());

                // set current item
                if (indexItem - 1 >= 0){
                    self.currentItem(self.items()[indexItem - 1]);
                } else {
                    self.currentItem(self.items()[0]);
                }
                // var item = self.items()[indexItem];

            };

            self.rmCurrentItemSliderSyncing = function (item, data) {
                var indexItem = self.items.indexOf(item());
                var indexCurrentItemSlider = data.slider.items.indexOf(data.slider.currentItem());
                var indexCurrentItemChildSlider = data.childSlider.items.indexOf(data.childSlider.currentItem());

                data.slider.removeItem(data.slider.items()[indexItem]);
                data.childSlider.removeItem(data.childSlider.items()[indexItem]);

                if (indexItem === indexCurrentItemSlider){
                    if (indexItem - 1 >= 0){
                        data.slider.currentItem(data.slider.items()[indexItem - 1]);
                    } else {
                        data.slider.currentItem(data.slider.items()[0]);
                    }
                }

                if (indexItem === indexCurrentItemChildSlider){
                    if (indexItem - 1 >= 0){
                        data.childSlider.currentItem(data.childSlider.items()[indexItem - 1]);
                    } else {
                        data.childSlider.currentItem(data.childSlider.items()[0]);
                    }
                }


            };

            ko.computed(function () {
                var numberItem = self.items().length;
                var options = [];
                for (var i = 1; i <= parseInt(numberItem); i++){
                    options.push(i);
                }
                self.optionChooseNumOfItemShow(options);
            }, this);
        }

        function Item(dataItem, config) {
            var self = this;

            var defaultBlocks = ko.toJSON(ko.observableArray([new Block(undefined)]));
            var defaultValue = {
                id: 0,
                orderId: 1,
                itemName: 'Slider Item 1',
                customImageUrl: {},
                bgImageType: 'customImage',
                bgImage: '',
                bgPosition: '',
                bgRepeat: '',
                bgSize: '',
                itemCustomClass: '',
                bgLink: '',
                sortOrder: '',
                paddingTop: 15,
                paddingRight: 15,
                paddingBottom: 15,
                paddingLeft: 15,
                marginTop: 0,
                marginRight: 0,
                marginBottom: 0,
                marginLeft: 0,
                contentPaddingTop: 15,
                contentPaddingRight: 15,
                contentPaddingBottom: 15,
                contentPaddingLeft: 15,
                contentMarginTop: 0,
                contentMarginRight: 0,
                contentMarginBottom: 0,
                contentMarginLeft: 0,
                contentWidth: 100,
                contentWidthUnit: '%',
                contentPosition: '',
                contentBgColor: '#000000',
                contentOpacity: 0,
                contentAnimation: '',
                contentAnimationDelay: 0,
                blocks: JSON.parse(defaultBlocks)
            };
            dataItem = replaceDefaultValue(defaultValue, dataItem);

            // item config
            self.id = dataItem.id;
            self.orderId = ko.observable(dataItem.orderId);
            self.categoryId = ko.observable(dataItem.categoryId);
            self.itemName = ko.observable(dataItem.itemName);

            self.imgCateUrl = ko.observable(dataItem.imgCateUrl);
            self.categoryTitle = ko.observable(dataItem.categoryTitle);
            self.categoryDesc = ko.observable(dataItem.categoryDesc);
            self.categoryUrl = ko.observable(dataItem.categoryUrl);
            self.customImageUrl = ko.observable(dataItem.customImageUrl);

            self.bgImageType = ko.observable(dataItem.bgImageType);
            self.bgImage = ko.observable(dataItem.bgImage);
            ko.computed(function () {
                if (self.bgImageType() === 'categoryImage') {
                    if (self.imgCateUrl() !== "" && self.imgCateUrl() !== undefined){
                        self.bgImage(self.imgCateUrl.peek());
                    } else {
                        if (self.categoryId() === undefined || self.imgCateUrl.peek() === ""){
                            alert({
                                content: $.mage.__('The category image not found!')
                            });
                        }
                        self.bgImageType('customImage');
                        self.bgImage("");
                    }
                }
                if (self.bgImageType() === 'customImage') {
                    if (!$.isEmptyObject(self.customImageUrl())){
                        self.bgImage('pub/media/catalog/tmp/category/' + self.customImageUrl().file);
                    } else {
                        self.bgImage("");
                    }
                }
            }, this);

            self.bgPosition = ko.observable(dataItem.bgPosition);
            self.bgRepeat = ko.observable(dataItem.bgRepeat);
            self.bgSize = ko.observable(dataItem.bgSize);

            self.height = ko.observable(dataItem.height).extend({ validateInput: {required: false, checkInt: false, checkNegaInt: true} });
            self.heightUnit = ko.observable(dataItem.heightUnit); // % or px
            self.width = ko.observable(dataItem.width).extend({ validateInput: {required: true, checkInt: false, checkNegaInt: true} });
            self.widthUnit = ko.observable(dataItem.widthUnit);

            self.imageUploadScope = 'imageUploadScope' + Math.floor(Math.random() * 10000000);
            self.imageUploaderMageInit = {'Magento_Ui/js/core/app': {'components': {}}};
            self.imageUploaderMageInit['Magento_Ui/js/core/app']['components'][this.imageUploadScope] = {
                'component': 'Magenest_Slider/js/model/file-uploader',
                'dataScope': 'cs.bgImage',
                elementTmpl: 'ui/form/element/uploader/uploader',
                'isMultipleFiles': false,
                template: 'ui/form/element/uploader/uploader',
                uploaderConfig: {
                    url: config.baseUrl + config.frontNameAdmin + '/slider/slider/uploadimage'
                },
                'lineItemImage': self.customImageUrl,
                'value': $.isEmptyObject(self.customImageUrl()) ? [] : [self.customImageUrl()],
            };

            self.chooseCategoryScope = 'categoryScope' + Math.floor(Math.random() * 10000000);
            self.chooseCategoryInit = {'Magento_Ui/js/core/app': {'components': {}}};
            self.chooseCategoryInit['Magento_Ui/js/core/app']['components'][this.chooseCategoryScope] = {
                'component': 'Magenest_Slider/js/model/choose-category',
                "dataScope": 'categoryId',
                "dataType": "select",
                "value": self.categoryId(),
                "getCategoryInfoUrl": config.baseUrl + config.frontNameAdmin + '/slider/slider/getcategoryinfo',
                "categoryId": self.categoryId,
                "categoryTitle": self.categoryTitle,
                "imgCateUrl": self.imgCateUrl,
                "categoryDesc": self.categoryDesc,
                "categoryUrl": self.categoryUrl,
                'options': config.listProducts,
            };

            // item config
            self.itemCustomClass = ko.observable(dataItem.itemCustomClass);
            self.bgLink = ko.observable(dataItem.bgLink);
            self.sortOrder = ko.observable(dataItem.sortOrder);
            self.paddingTop = ko.observable(dataItem.paddingTop).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: true} });
            self.paddingRight = ko.observable(dataItem.paddingRight).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: true} });
            self.paddingBottom = ko.observable(dataItem.paddingBottom).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: true} });
            self.paddingLeft = ko.observable(dataItem.paddingLeft).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: true} });

            self.marginTop = ko.observable(dataItem.marginTop).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: false} });
            self.marginRight = ko.observable(dataItem.marginRight).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: false} });
            self.marginBottom = ko.observable(dataItem.marginBottom).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: false} });
            self.marginLeft = ko.observable(dataItem.marginLeft).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: false} });

            // item content config
            self.contentPaddingTop = ko.observable(dataItem.contentPaddingTop).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: true} });
            self.contentPaddingRight = ko.observable(dataItem.contentPaddingRight).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: true} });
            self.contentPaddingBottom = ko.observable(dataItem.contentPaddingBottom).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: true} });
            self.contentPaddingLeft = ko.observable(dataItem.contentPaddingLeft).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: true} });

            self.contentMarginTop = ko.observable(dataItem.contentMarginTop).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: false} });
            self.contentMarginRight = ko.observable(dataItem.contentMarginRight).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: false} });
            self.contentMarginBottom = ko.observable(dataItem.contentMarginBottom).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: false} });
            self.contentMarginLeft = ko.observable(dataItem.contentMarginLeft).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: false} });

            self.contentWidth = ko.observable(dataItem.contentWidth).extend({ validateInput: {required: true, checkInt: false, checkNegaInt: true} });
            self.contentWidthUnit = ko.observable(dataItem.contentWidthUnit);
            self.contentHeight = ko.observable(dataItem.contentHeight);
            self.contentHeightUnit = ko.observable(dataItem.contentHeightUnit);

            ko.computed(function () {
                if (self.contentWidthUnit() === '%'){
                    if(parseInt(self.contentWidth()) < parseInt(0) || isNaN(self.contentWidth()))
                        self.contentWidth(0);
                    else if(parseInt(self.contentWidth()) >  parseInt(100))
                        self.contentWidth(100);
                }
                if (self.contentHeightUnit() === '%'){
                    if(parseInt(self.contentHeight()) < parseInt(0) || isNaN(self.contentHeight()))
                        self.contentHeight(0);
                    else if(parseInt(self.contentHeight()) >  parseInt(100))
                        self.contentHeight(100);
                }
                if(parseInt(self.contentHeight()) < parseInt(0) || isNaN(self.contentHeight()))
                    self.contentHeight("");
            }, this);

            self.contentPosition = ko.observable(dataItem.contentPosition);
            self.contentBgColor = ko.observable(dataItem.contentBgColor);
            self.contentOpacity = ko.observable(dataItem.contentOpacity);
            ko.computed(function () {
                if(parseFloat(self.contentOpacity()) < parseInt(0) || isNaN(self.contentOpacity()))
                    self.contentOpacity(0);
                else if(parseFloat(self.contentOpacity()) >  parseInt(1))
                    self.contentOpacity(1);
            }, this);

            self.contentAnimation = ko.observable(dataItem.contentAnimation);
            self.contentAnimationDelay = ko.observable(dataItem.contentAnimationDelay).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: true} });


            // this config for preview backend, not save to database
            self.contentBgColorRgb = ko.computed(function () {
                var hex = self.contentBgColor().replace('#','');
                var r = parseInt(hex.substring(0,2), 16);
                var g = parseInt(hex.substring(2,4), 16);
                var b = parseInt(hex.substring(4,6), 16);
                return 'rgba('+r+','+g+','+b+','+ self.contentOpacity()+')';
            });

            self.maxWidthContent = ko.computed(function () {
                var position = self.contentPosition();
                if (position === 'center'){
                    return '100%';
                }

                var total = parseInt(self.contentMarginLeft()) + parseInt(self.contentMarginRight());
                return 'calc(100% - ' + total + 'px)';
            });

            self.marginLeftContent = ko.computed(function () {
                var position = self.contentPosition();
                if (position === 'center'){
                    return 'auto';
                }
                return self.contentMarginLeft() + 'px';
            });

            self.marginRightContent = ko.computed(function () {
                var position = self.contentPosition();
                if (position === 'center'){
                    return 'auto';
                }
                return self.contentMarginRight() + 'px';
            });

            self.floatContent = ko.computed(function () {
                var position = self.contentPosition();
                if (position === 'right'){
                    return 'right';
                }
                return 'none';
            });


            // block content
            var blocks = [];
            $.each(dataItem.blocks, function (index, block) {
                blocks.push(new Block(block, {
                   'categoryId': self.categoryId,
                   'categoryTitle': self.categoryTitle,
                   'categoryDesc': self.categoryDesc,
                   'categoryUrl': self.categoryUrl,
                }));
            });

            // block setting
            self.blocks = ko.observableArray(blocks);

            self.addBlock = function (type) {
                var newBlock = new Block({type: type}, {
                    'categoryId': self.categoryId,
                    'categoryTitle': self.categoryTitle,
                    'categoryDesc': self.categoryDesc,
                    'categoryUrl': self.categoryUrl,
                });
                self.blocks.push(newBlock);
                self.blockSelecting(self.blocks.indexOf(newBlock))
            };

            self.removeBlock = function (block) {
                self.blocks.remove(block);
            };

            self.blockSelecting = ko.observable(undefined);

            self.editBlock = function(currentItem, block){
                var idBlock = self.blocks.indexOf(block);
                self.blockSelecting(idBlock);
            }
        }

        function Block(dataBlock, cateData) {
            var self = this;
            var defaultValue = {
                type: 'html',
                customClass: '',
                position: 'left',
                textAlign: 'left',
                contentType: 'custom',
                content: "Content box: Please enter content of box", // use for text, html, button
                fontSize: '14',
                textColor: "#F26321",
                textColorHover: "#ffffff",
                bgColor: "#ffffff",
                bgColorHover: "#F26321",
                borderColor: "#F26321",
                borderColorHover: "#F26321",
                btnUrl: 'customLink',
                customLink: '',
                animation: '',
                animationDelay: 0,
                paddingTop: 0,
                paddingRight: 0,
                paddingBottom: 0,
                paddingLeft: 0,
                marginTop: 0,
                marginRight: 0,
                marginBottom: 0,
                marginLeft: 0,
                width: 100,
                widthUnit: '%',
                customText: '',
                customHtml: ''
            };

            dataBlock = replaceDefaultValue(defaultValue, dataBlock);

            self.blockId = 'block-' + Math.floor(Math.random() * 10000000);
            self.type = ko.observable(dataBlock.type);
            self.customClass = ko.observable(dataBlock.customClass);
            self.position = ko.observable(dataBlock.position);
            self.textAlign = ko.observable(dataBlock.textAlign);

            self.contentType = ko.observable(dataBlock.contentType);
            self.content = ko.observable(dataBlock.content);

            self.fontSize = ko.observable(dataBlock.fontSize).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: true} });
            self.textColor = ko.observable(dataBlock.textColor);
            self.textColorHover = ko.observable(dataBlock.textColorHover);
            self.bgColor = ko.observable(dataBlock.bgColor);
            self.bgColorHover = ko.observable(dataBlock.bgColorHover);
            self.borderColor = ko.observable(dataBlock.borderColor);
            self.borderColorHover = ko.observable(dataBlock.borderColorHover);
            self.btnUrl = ko.observable(dataBlock.btnUrl);
            self.customLink = ko.observable(dataBlock.customLink);

            self.animation = ko.observable(dataBlock.animation);
            self.animationDelay = ko.observable(dataBlock.animationDelay).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: true} });

            self.paddingTop = ko.observable(dataBlock.paddingTop).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: true} });
            self.paddingRight = ko.observable(dataBlock.paddingRight).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: true} });
            self.paddingBottom = ko.observable(dataBlock.paddingBottom).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: true} });
            self.paddingLeft = ko.observable(dataBlock.paddingLeft).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: true} });

            self.marginTop = ko.observable(dataBlock.marginTop).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: false} });
            self.marginRight = ko.observable(dataBlock.marginRight).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: false} });
            self.marginBottom = ko.observable(dataBlock.marginBottom).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: false} });
            self.marginLeft = ko.observable(dataBlock.marginLeft).extend({ validateInput: {required: true, checkInt: true, checkNegaInt: false} });

            self.width = ko.observable(dataBlock.width).extend({ validateInput: {required: true, checkInt: false, checkNegaInt: true} });
            self.widthUnit = ko.observable(dataBlock.widthUnit);

            ko.computed(function () {
                if (self.widthUnit() === '%'){
                    if(parseInt(self.width()) < parseInt(0) || isNaN(self.width()))
                        self.width(0);
                    else if(parseInt(self.width()) >  parseInt(100))
                        self.width(100);
                }

                // update title, url, desc, content if change category
                if (self.type() !== 'html'){
                    if(self.contentType() == 'title' && cateData.categoryId()){
                        self.content(cateData.categoryTitle());
                    } else if (self.contentType() == 'desc' && cateData.categoryId()){
                        self.content(cateData.categoryDesc());
                    } else if (self.contentType() == 'custom') {
                        self.content(self.content());
                    } else {
                        alert({
                            title: 'Some thing error',
                            content: 'You haven\'t selected category. Please select category in Item Configurations menu',
                        });
                        self.contentType('custom');
                    }
                }
                if (self.type() === 'button'){
                    if(self.btnUrl() == 'categoryLink' && cateData.categoryId()) {
                        self.customLink(cateData.categoryUrl());
                    } else if (self.btnUrl() == 'customLink'){
                        self.customLink(self.customLink());
                    } else {
                        alert({
                            title: 'Some thing error',
                            content: 'You haven\'t selected category. Please select category in Item Configurations menu',
                        });
                        self.btnUrl('customLink');
                    }
                }

            }, this);

            // this config for preview backend, not save to database
            self.maxWidthBlock = ko.computed(function () {
                var position = self.position();
                if (position === 'center'){
                    return '100%';
                }

                var total = parseInt(self.marginLeft()) + parseInt(self.marginRight());
                return 'calc(100% - ' + total + 'px)';
            });

            self.marginLeftBlock = ko.computed(function () {
                var position = self.position();
                if (position === 'center'){
                    return 'auto';
                }
                return self.marginLeft() + 'px';
            });

            self.marginRightBlock = ko.computed(function () {
                var position = self.position();
                if (position === 'center'){
                    return 'auto';
                }
                return self.marginRight() + 'px';
            });

            self.floatContentBlock = ko.computed(function () {
                var position = self.position();
                if (position === 'right'){
                    return 'right';
                }
                return 'none';
            });
        }

        function compareVersion(v1, v2) {
            if (typeof v1 !== 'string') return false;
            if (typeof v2 !== 'string') return false;
            v1 = v1.split('.');
            v2 = v2.split('.');
            const k = Math.min(v1.length, v2.length);
            for (let i = 0; i < k; ++ i) {
                v1[i] = parseInt(v1[i], 10);
                v2[i] = parseInt(v2[i], 10);
                if (v1[i] > v2[i]) return 1;
                if (v1[i] < v2[i]) return -1;
            }
            return v1.length == v2.length ? 0: (v1.length < v2.length ? -1 : 1);
        }

    return Component.extend({
        defaults: {
            scope: 'main-slider',
            template: 'Magenest_Slider/main',
        },

        openEditor: function(obj, slider){
            this.editorActivating(obj);
            this.editorStatus(true);
            var ele = obj +"-settings";
            $("#" + ele).collapsible("activate");
        },
        closeEditor: function(){
            this.editorStatus(false);
        },

        initWysiwyg: function(version, config, block){
            var wysiwyg;

            if (compareVersion(version, '2.3.0') < 0){
                wysiwyg = new tinyMceWysiwygSetup(block.blockId, config);
                ko.computed(function () {
                    if (block.type() === 'html'){
                        wysiwyg.turnOn();
                        tinymce.execCommand('mceSetContent', false, block.content.peek());
                    } else {
                        var textHtml = $('<p>' + block.content.peek() + '</p>').text();
                        block.content(textHtml);
                    }
                });

                $('.toggleWysiwyg').click(function () {
                    wysiwyg.toggle();
                    return false;
                });
            } else {
                wysiwyg = new wysiwygSetup(block.blockId,config);
                ko.computed(function () {
                    if (block.type() === 'html'){
                        wysiwyg.setup("exact");
                        tinymce.execCommand('mceSetContent', false, block.content.peek());
                    } else {
                        var textHtml = $('<p>' + block.content.peek() + '</p>').text();
                        block.content(textHtml);
                    }
                });

                $('.toggleWysiwyg').click(function () {
                    tinymce.execCommand('mceToggleEditor',false, block.blockId);
                    return false;
                });
            }
            $('input').keydown(function (e) {
                if (e.which === 13){
                    return false;
                }
            });
        },

        initialize: function (config) {
            var self = this;

            this.magentoVersion = config.magentoVersion;
            this.wysiwygConfig = config.wysiwygConfig;
            this.previewUrl = config.previewUrl;
            this.baseUrl = config.baseUrl;
            this.frontNameAdmin = config.frontNameAdmin;
            this.status = ko.observable(parseInt(config.sliderData.status));
            this.sliderName = ko.observable(config.sliderData.sliderName);
            this.type = ko.observable(config.sliderData.type);
            this.previousType = ko.observable(config.sliderData.type);
            this.type.subscribe(function (oldValue) {
                self.previousType(oldValue);
            }, null, "beforeChange");

            this.sliderConfig = ko.computed(function () {
                return {
                    'status': self.status(),
                    'sliderName': self.sliderName(),
                    'type': self.type(),
                }
            });

            this.editorActivating = ko.observable('slider');
            this.editorStatus = ko.observable(false);
            this.showEditorSlider = ko.pureComputed(function () {
                return (this.editorStatus() === true) ? 'slider-secondary col-l-4' : 'slider-secondary hidden'
            }, this);

            this.fullSlider = ko.pureComputed(function () {
                return (this.editorStatus() === true) ? 'slider-main col-l-8' : 'slider-main'
            }, this);

            // on/off choose add new content
            this.detailsEnabled = ko.observable(false);
            this.toggleAddContent = function() {
                if (this.detailsEnabled() === true){
                    this.detailsEnabled(false);
                } else {
                    this.detailsEnabled(true);
                }
            };

            this.sliderId = ko.observable(config.sliderId);
            this.slider = new Slider(config.sliderData, config);
            this.childSliderId = ko.observable(config.childSliderId);
            this.childSlider = new Slider(config.childSliderData, config);

            // if slider is 2 sliders will set some attr is the same
            this.childSlider.customClass = ko.computed(function () {
                return self.slider.customClass();
            });
            this.childSlider.customCss = ko.computed(function () {
                return self.slider.customCss();
            });
            this.childSlider.autoPlay = ko.computed(function () {
                return self.slider.autoPlay();
            });
            this.childSlider.autoPlaySpeed = ko.computed(function () {
                return self.slider.autoPlaySpeed();
            });
            this.childSlider.pauseHover = ko.computed(function () {
                return self.slider.pauseHover();
            });

            this.currentSlider = ko.observable(this.slider);

            this.switchSlider = function (slider) {
                this.currentSlider(slider);
            };

            this.itemKept = ko.observable(this.slider.items()[0]); // use when change type of slider from slide to banner

            this.changeTypeSlide = function (data) {
                if (data.type() === "2"){
                    // change type to Slider Syncing

                    while (data.childSlider.items().length < data.slider.items().length){
                        data.childSlider.addItem();
                    }
                }

                if (data.previousType() === "2"){
                    confirmation({
                        title: 'Are you sure?',
                        content: 'If you change from Syncing slider, the slider 2 will be deleted! Are you sure?',
                        actions: {
                            confirm: function(){
                                if (data.type() === "0"){
                                    // change type from slider to banner
                                    if (data.slider.items().length > 1){
                                        // show popup to select the item be kept
                                        $('#select-kept-item').modal({
                                            buttons: [{
                                                text: 'Done',
                                                click: function() {
                                                    var itemKept = self.itemKept();
                                                    var listItemDelete = [];
                                                    $.each(data.slider.items(), function (index, item) {
                                                        if (itemKept !== item){
                                                            listItemDelete.push(item);
                                                        }
                                                    });
                                                    $.each(listItemDelete, function (index, item) {
                                                        data.slider.removeItem(item);
                                                    });
                                                    data.slider.currentItem(itemKept);
                                                    data.currentSlider(data.slider);
                                                    data.childSlider.items([]);
                                                    this.closeModal();
                                                }
                                            },
                                                {
                                                    text: 'Cancel',
                                                    click: function () {
                                                        this.closeModal();
                                                    }
                                                }],
                                            closed: function(){
                                                if (!(data.type() === '0' && data.slider.items().length === 1)){
                                                    data.type(data.previousType());
                                                }
                                            }
                                        });
                                        $('#select-kept-item').modal('openModal')
                                    }
                                }

                                if (data.type() === "1"){
                                    data.currentSlider(data.slider);
                                    data.childSlider.items([]);
                                }
                            },
                            cancel: function(){
                                data.type("2");
                            },
                        }
                    });

                } else {
                    if (data.type() === "0"){
                        // change type from slider to banner
                        if (data.slider.items().length > 1){
                            // show popup to select the item be kept
                            $('#select-kept-item').modal({
                                buttons: [{
                                    text: 'Done',
                                    click: function() {
                                        var itemKept = data.itemKept();
                                        var listItemDelete = [];
                                        $.each(data.slider.items(), function (index, item) {
                                            if (itemKept !== item){
                                                listItemDelete.push(item);
                                            }
                                        });
                                        $.each(listItemDelete, function (index, item) {
                                            data.slider.removeItem(item);
                                        });
                                        data.slider.currentItem(itemKept);
                                        this.closeModal();
                                    }
                                },
                                    {
                                        text: 'Cancel',
                                        click: function () {
                                            this.closeModal();
                                        }
                                    }],
                                closed: function(){
                                    if (!(data.type() === '0' && data.slider.items().length === 1)){
                                        data.type(data.previousType());
                                    }
                                }
                            });
                            $('#select-kept-item').modal('openModal');
                        }
                    }
                }
            };
            self.bindAction();
            this._super();
        },

        sortTable: function (data) {
            $('#blocks-list').sortable({
                cursor: "move",

                update: function( event, ui ) {
                    var blocks = data.currentSlider().currentItem().blocks();
                    var block_ids = $( '#blocks-list' ).sortable( "toArray" );
                    var blocks_sorted = [];

                    $.each(block_ids, function (index, block_id) {
                        blocks_sorted.push(blocks[block_id]);
                    });

                    data.currentSlider().currentItem().blocks(blocks_sorted);
                }
            });
        },

        bindAction: function () {
            var self = this;
            $('#preview').on('click', function (event) {
                window.open('about:blank', 'myPage');

                $.when($.ajax({
                    method: 'POST',
                    url: '' + self.previewUrl,
                    showLoader: true,
                    data: {
                        preview_data: JSON.stringify($('#edit_form').serializeArray())
                    }
                })).done(function (response) {
                    if (response.error) {
                        alert(response.message);
                    } else {
                        window.open(self.baseUrl + 'slider/sliderpreview?key=' + response.token, 'myPage');
                    }
                });
            });
        }
    });

    }
);

