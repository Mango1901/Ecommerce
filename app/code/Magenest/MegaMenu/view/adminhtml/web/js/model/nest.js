/*jshint browser:true jquery:true*/
/*global alert*/
define([
        'jquery',
        'underscore',
        'ko',
        'Magento_Ui/js/modal/modal',
        'tinymce',
        'mage/adminhtml/wysiwyg/tiny_mce/setup',
        'colorpicker'
    ], function ($, _, ko, modal, tinyMCE) {
        return function (data) {
            var isGrid = false,
                isContent = false,
                isSubCat = false;
            var checkIssetWithCondition = function (checkingData, condition = '0') {
                return _.isUndefined(checkingData) || checkingData === null || checkingData === condition;
            };
            data.canChangeLink = data.menu_type === '3';

            if (data.mainEnable == "1") {
                data.showMain = true;
                if (checkIssetWithCondition(data.mainContentType, 'grid')) {
                    data.mainContentType = 'grid';
                    isGrid = true;
                }
                if (data.mainColumn === null) {
                    data.mainColumn = 4;
                }
            } else {
                data.mainEnable = '0';
                data.showMain = false;
                data.mainContentType = null;
            }

            if (data.mainContentType === 'grid') {
                isGrid = true;
            } else if (data.mainContentType === 'content') {
                isContent = true;
            } else if (data.mainContentType === 'sub-category') {
                isGrid = true;
                isSubCat = true;
            }

            if (checkIssetWithCondition(data.leftEnable)) {
                data.showLeft = false;
                data.leftEnable = '0';
            } else {
                data.showLeft = true;
            }

            if (checkIssetWithCondition(data.rightEnable)) {
                data.showRight = false;
                data.rightEnable = '0';
            } else {
                data.showRight = true;
            }

            if (checkIssetWithCondition(data.headerEnable)) {
                data.showHeader = false;
                data.headerEnable = '0';
            } else {
                data.showHeader = true;
            }

            if (checkIssetWithCondition(data.footerEnable)) {
                data.showFooter = false;
                data.footerEnable = '0';
            } else {
                data.showFooter = true;
            }

            return {
                title: ko.observable(data.title),
                id: ko.observable(data.id),
                sort: ko.observable(data.sort),
                level: ko.observable(data.level),
                levelTemp: ko.observable(1),
                parent: ko.observable(data.parent),
                parentNode: ko.observable(data.parentNode),
                is_new: ko.observable(data.is_new),
                type: ko.observable(data.type),
                obj_id: ko.observable(data.obj_id),
                hasBrother: ko.observable(data.hasBrother),
                hasParent: ko.observable(data.hasParent),
                megaColumn: ko.observable(data.megaColumn),
                link: ko.observable(data.link),
                brother_name: ko.observable(data.brother_name),
                brother_id: ko.observable(data.brother_id),
                parent_name: ko.observable(data.parent_name),
                parentName: ko.observable(data.parentName),
                include_child: ko.observable(data.include_child),
                open_setting: ko.observable('in-active'),
                needUpdateBrother: ko.observable(false),
                origLevel: ko.observable(data.level),
                menu_type: ko.observable(data.menu_type),
                canChangeLink: ko.observable(data.canChangeLink),

                isSelected: ko.observable(false),
                cssClass: ko.observable(data.cssClass),
                showIcon: ko.observable(data.showIcon),
                icon: ko.observable(data.icon),
                label: ko.observable(data.label),
                cssInline: ko.observable(data.cssInline),

                /** Attribute about position and relation with brother and parent */
                isTop: ko.observable(false),
                isBottom: ko.observable(false),
                children: ko.observableArray(),
                flatChildren: ko.observableArray(),
                width: ko.observable(data.width),

                /** Left Block Attribute **/
                leftEnable: ko.observable(data.leftEnable),
                leftClass: ko.observable(data.leftClass),
                leftWidth: ko.observable(data.leftWidth),
                leftContentHtml: ko.observable(data.leftContentHtml),

                /** Main Block Content Attribute **/
                mainEnable: ko.observable(data.mainEnable),
                mainProductSku: ko.observable(data.mainProductSku),
                mainInCategory: ko.observable(data.mainInCategory),
                mainContentType: ko.observable(data.mainContentType),
                mainContentWidth: ko.observable(data.mainContentWidth),
                mainParentCategory: ko.observable(data.mainParentCategory),
                mainColumn: ko.observable(data.mainColumn),
                mainContentHtml: ko.observable(data.mainContentHtml),

                /** Right Block **/
                rightEnable: ko.observable(data.rightEnable),
                rightClass: ko.observable(data.rightClass),
                rightWidth: ko.observable(data.rightWidth),
                rightContentHtml: ko.observable(data.rightContentHtml),

                /** Header Block **/
                headerEnable: ko.observable(data.headerEnable),
                headerClass: ko.observable(data.headerClass),
                headerContentHtml: ko.observable(data.headerContentHtml),

                /** Footer Block **/
                footerEnable: ko.observable(data.footerEnable),
                footerClass: ko.observable(data.footerClass),
                footerContentHtml: ko.observable(data.footerContentHtml),

                /** Style Attribute **/
                color: ko.observable(data.color),
                textColor: ko.observable(data.textColor),
                hoverTextColor: ko.observable(data.hoverTextColor),
                hoverIconColor: ko.observable(data.hoverIconColor),
                backgroundImage: ko.observable(data.backgroundImage),
                backgroundRepeat: ko.observable(data.backgroundRepeat),
                backgroundColor: ko.observable(data.backgroundColor),
                backgroundSize: ko.observable(data.backgroundSize),
                backgroundOpacity: ko.observable(data.backgroundOpacity),
                backgroundPositionX: ko.observable(data.backgroundPositionX),
                backgroundPositionY: ko.observable(data.backgroundPositionY),
                animationDelayTime: ko.observable(data.animationDelayTime),
                animationSpeed: ko.observable(data.animationSpeed),
                animationIn: ko.observable(data.animationIn),
                serializedChildren: ko.observable(),

                /** Style main content**/
                showGrid: ko.observable(isGrid),
                showContent: ko.observable(isContent),
                showSubCat: ko.observable(isSubCat),
                showMain: ko.observable(data.showMain),
                showLeft: ko.observable(data.showLeft),
                showRight: ko.observable(data.showRight),
                showHeader: ko.observable(data.showHeader),
                showFooter: ko.observable(data.showFooter),
                data: ko.observable(null),
                isActive: ko.observable(false),
                itemEnable: ko.observable(data.itemEnable),
                linkTarget: ko.observable(data.linkTarget),
                hideText: ko.observable(data.hideText),

                getNest: function () {
                    return this;
                },

                /**
                 * Init image uploader component
                 */
                initImageUploaderComponent: function () {
                    //Icon
                    this.iconUploaderDataScope = 'item-icon-' + this.id() + '-image-uploader';
                    this.iconUploaderMageInit = {'Magento_Ui/js/core/app': {'components': {}}};
                    this.iconUploaderMageInit['Magento_Ui/js/core/app']['components'][this.iconUploaderDataScope] = {
                        'component': 'Magenest_MegaMenu/js/model/file-uploader',
                        'lineItemImage': this.icon,
                        'value': [this.icon()],
                        'dataScope': 'lineItem.image'
                    };
                    // Label
                    this.labelSelectorDataScope = 'item-' + this.id() + '-label-selector';
                    this.labelSelectorMageInit = {
                        'Magento_Ui/js/core/app': {
                            'components': {
                                [this.labelSelectorDataScope]: {
                                    'component': 'Magenest_MegaMenu/js/model/label-selector',
                                    'value': this.label(),
                                    'nest': this.getNest.bind(this)
                                }
                            }
                        }
                    };
                    //Background
                    this.backgroundUploaderDataScope = 'item-background-' + this.id() + '-image-uploader';
                    this.backgroundUploaderMageInit = {'Magento_Ui/js/core/app': {'components': {}}};
                    this.backgroundUploaderMageInit['Magento_Ui/js/core/app']['components'][this.backgroundUploaderDataScope] = {
                        'component': 'Magenest_MegaMenu/js/model/file-uploader',
                        'lineItemImage': this.backgroundImage,
                        'value': [this.backgroundImage()],
                        'dataScope': 'lineItem.image'
                    };
                },

                /**
                 * Create data
                 */
                getSerializeChildren: function () {
                    var self = this,
                        out = 0;

                    ko.utils.arrayForEach(self.flatChildren(), function (item) {
                        out += ',' + item;
                    });

                    this.serializedChildren(out);
                },

                renderObservableArray: function (input) {
                    return JSON.stringify(input);
                },

                /**
                 * Create Data
                 */
                createData: function () {
                    this.data(JSON.stringify({
                        id: this.id(),
                        children: this.serializedChildren(),
                        obj_id: this.obj_id(),
                        level: this.levelTemp(),
                        sort: this.sort(),
                        isTop: this.isTop(),
                        is_bottom: this.isBottom(),
                        parent: this.parent(),
                        parent_id: this.parent(),
                        megaColumn: this.megaColumn(),
                        type: this.type(),
                        link: this.link(),
                        is_new: this.is_new(),
                        icon: (!_.isUndefined(this.icon())) ? this.icon()['name'] : '',
                        label: this.label(),
                        title: this.title(),
                        brother_name: this.brother_name(),
                        include_child: this.include_child(),
                        cssClass: this.cssClass(),
                        showIcon: this.showIcon(),
                        leftEnable: this.leftEnable(),
                        leftClass: this.leftClass(),
                        leftWidth: this.leftWidth(),
                        mainEnable: this.mainEnable(),
                        mainProductSku: this.mainProductSku(),
                        mainInCategory: this.mainInCategory(),
                        mainContentType: this.mainContentType(),
                        mainColumn: this.mainColumn(),
                        rightEnable: this.rightEnable(),
                        rightClass: this.rightClass(),
                        rightWidth: this.rightWidth(),
                        headerEnable: this.headerEnable(),
                        headerClass: this.headerClass(),
                        footerEnable: this.footerEnable(),
                        footerClass: this.footerClass(),
                        color: this.color(),
                        textColor: this.textColor(),
                        hoverTextColor: this.hoverTextColor(),
                        hoverIconColor: this.hoverIconColor(),
                        background_image: (!_.isUndefined(this.backgroundImage())) ? this.backgroundImage()['name'] : '',
                        backgroundRepeat: this.backgroundRepeat(),
                        backgroundColor: this.backgroundColor(),
                        backgroundSize: this.backgroundSize(),
                        backgroundOpacity: this.backgroundOpacity(),
                        itemEnable: this.itemEnable(),
                        linkTarget: this.linkTarget(),
                        cssInline: this.cssInline(),
                        hideText: this.hideText(),
                        background_position_x: this.backgroundPositionX(),
                        background_position_y: this.backgroundPositionY(),
                        animateDelayTime: this.animationDelayTime(),
                        animateSpeed: this.animationSpeed(),
                        animation_in: this.animationIn(),
                        main_content_width: this.mainContentWidth(),
                        parent_category_content: this.mainParentCategory(),
                        menu_type: this.menu_type()
                    }));
                },

                /**
                 * Get title
                 * @returns {*}
                 */
                getTitle: function () {
                    return this.title;
                },
                /**
                 * Get link
                 * @returns {*}
                 */
                getLink: function () {
                    return this.link;
                },
                /**
                 * Set link
                 * @param link
                 */
                setLink: function (link) {
                    this.link(link);
                },
                /**
                 * Get Item Enabled
                 */
                getItemEnable: function (item) {
                   return (this.itemEnable() == "1") ? 'enabled' : 'disabled';
                },
                /**
                 * Edit menu
                 */
                myEdit: function () {
                    var self = this;
                    ko.utils.arrayForEach(self.parentLevel1.nestedMenu(), function (item) {
                        item.isSelected(false);
                        item.isActive(false);
                    });

                    self.isActive(true);
                    self.isSelected(true);

                    if ($('#nestedMenu').length) {
                        $('#nestedMenu').removeClass('menu-settings-open');
                    }

                    $('html, body').animate({scrollTop: 241}, 'slow');
                },
                /**
                 * Remove menu
                 */
                myRemove: function () {
                    var menu = this,
                        menuId = menu.id();

                    menu.parentLevel1.removedItems.push(menuId);
                    menu.parentLevel1.nestedMenu.remove(menu);
                    $('li.dd-item[data-id="' + menuId + '"]').remove();

                    ko.utils.arrayForEach(menu.children(), function (item) {
                        var item3 = _.find(menu.parentLevel1.nestedMenu(), function (item2) {
                            return item2.id() == item.id();
                        });

                        if (item3) {
                            item3.myRemove();
                        }
                    });
                },
                /**
                 * Event type enable
                 */
                eventTypeEnable: function () {
                    this.showSubCat(false);
                    this.showContent(false);
                    this.showGrid(false);
                    if (this.mainContentType() === 'grid') {
                        this.showGrid(true);
                    } else if (this.mainContentType() === 'content') {
                        this.showContent(true);
                    } else if (this.mainContentType() === 'sub-category') {
                        this.showGrid(true);
                        this.showSubCat(true);
                    }
                },
                /**
                 * Main enable
                 */
                eventMainEnable: function () {
                    this.eventTypeEnable();

                    if (this.mainEnable() === '1') {
                        this.showMain(true);
                    } else {
                        this.showSubCat(false);
                        this.showMain(false);
                        this.showGrid(false);
                        this.showContent(false);
                    }
                },
                /**
                 * Slide left enable
                 */
                eventSideLeftEnable: function () {
                    if (this.leftEnable() === '1') {
                        this.showLeft(true);
                    } else {
                        this.showLeft(false);
                    }
                },
                /**
                 * Slide right enable
                 */
                eventSideRightEnable: function () {
                    if (this.rightEnable() === '1') {
                        this.showRight(true);
                    } else
                        this.showRight(false);
                },
                /**
                 * Slide footer enable
                 */
                eventSideFooterEnable: function () {
                    if (this.footerEnable() === '1') {
                        this.showFooter(true);
                    } else
                        this.showFooter(false);
                },
                /**
                 * Slide header enable
                 */
                eventSideHeaderEnable: function () {
                    if (this.headerEnable() === '1') {
                        this.showHeader(true);
                    } else
                        this.showHeader(false);
                },
                /**
                 * Show editor
                 * @param $self
                 * @param $target
                 */
                showEditor: function ($self, $target) {
                    var data;

                    window.editorData = $self[$target.target.id];
                    data = (!_.isUndefined(window.editorData()) && !_.isNull(window.editorData())) ? window.editorData() : '';

                    if (_.isUndefined(window.editor)) {
                        window.editor = tinyMCE.get('_versatile_editor');
                    }

                    window.wysimyg_editor_modal.setTitle($target.target.dataset.title);
                    window.wysimyg_editor_modal.openModal();

                    if (!_.isUndefined(tinyMCE.get('_versatile_editor')) && tinyMCE.get('_versatile_editor') !== null) {
                        tinyMCE.get('_versatile_editor').setContent(data);
                    } else {
                        $('#_versatile_editor').val(data)
                    }
                },

                getCategoryOptions: function () {
                    return window.megaOptionConfig.categoryList;
                }
            };
        }
    }
);