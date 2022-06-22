define([
        'jquery',
        "underscore",
        'uiComponent',
        'ko',
        'page',
        'category',
        'nest',
        'Magento_Ui/js/modal/modal',
        'Magenest_MegaMenu/js/model/menu_handler',
        'mage/translate',
        'Magento_Variable/variables'
    ], function ($, _, Component, ko, page, category, nest, modal, menuHandler, $t) {
        'use strict';

        return Component.extend({
            defaults: {
                scope: 'nestedMenu',
                template: 'Magenest_MegaMenu/nestedmenu'
            },
            levelHolder: 0,
            menuId: 0,
            menuName: '',
            storeId: 1,
            isActive: 1,
            pages: [],
            selectedPage: [],
            chosenPages: [],
            currentMenuItem: '',
            saveUrl: 'http://localhost',
            formKey: 'formKey',
            cats: [],
            selectedCats: [],
            chosenCats: [],
            custom_link_title: '',
            custom_link_link: '',
            menus: [],
            removedItems: [],
            serializedRemovedItems: '',
            nestedMenu: [],

            /**
             * init data for the menu
             */
            initObservable: function () {
                this._super()
                    .observe({
                        isLoading: true,
                        levelHolder: 0,
                        menuId: 0,
                        menuName: '',
                        storeId: 1,
                        isActive: 1,
                        selectedPage: [],
                        chosenPages: [],
                        saveUrl: '',
                        formKey: '',
                        pages: [],
                        cats: [],
                        selectedCats: [],
                        chosenCats: [],
                        currentMenuItem: [],
                        custom_link_title: '',
                        custom_link_link: '',
                        menus: [],
                        hasBrother: true,
                        removedItems: [],
                        serializedRemovedItems: '',
                        tooltipImage: '',
                        colorImage: '',
                        nestedMenu: [],
                        searchCategory: ''
                    });


                var self = this,
                    menuConfig,
                    mappedPages,
                    mappedCats;

                menuConfig = window.megaMenuConfig;
                self.saveUrl(menuConfig.save_url);
                //show page
                mappedPages = $.map(menuConfig.pages, function (item) {
                    var pageObj = new page(item);

                    pageObj.parentLevel1 = self;

                    return pageObj
                });

                self.pages(mappedPages);
                //show cat
                mappedCats = $.map(menuConfig.cats, function (item) {
                    var catObj = new category(item);

                    catObj.parentLevel1 = self;

                    return catObj
                });

                self.cats(mappedCats);
                self.tooltipImage(menuConfig.tooltip_img);
                self.colorImage(menuConfig.color_img);

                /** Init the menu items */
                if (!_.isUndefined(menuConfig.menu_item_nest)
                    && menuConfig.menu_item_nest.length) {
                    $.each(menuConfig.menu_item_nest, function (index, item) {
                        self.initNestItem(item);
                    });

                    self.sortItems();
                    var nestedMenuZeroSort = _.find(self.nestedMenu(), function (item) {
                        return item.sort() === 0;
                    });
                    if (!_.isUndefined(nestedMenuZeroSort)) {
                        nestedMenuZeroSort.myEdit();
                    }
                }

                /** init the currentMenu which is a empty menu item */
                self.serializedRemovedItems = ko.pureComputed(function () {
                    var serializedr = '';

                    ko.utils.arrayForEach(self.removedItems(), function (item, i) {
                        if (i === 0) {
                            serializedr = item + ',';
                        } else if (i > 0 && i < self.removedItems().length - 1) {
                            serializedr = serializedr + item + ',';
                        } else {
                            serializedr = serializedr + item;
                        }
                    });

                    return serializedr;
                }, self);

                this.searchCategory.subscribe(function (value) {
                    ko.utils.arrayForEach(self.cats(), function (item) {
                        if (item.title().toUpperCase().indexOf(value.toUpperCase()) != -1) {
                            item.visible(true);
                        } else {
                            item.visible(false);
                        }
                    });
                });

                window.wysimyg_editor_modal = modal({
                    title: '',
                    innerScroll: true,
                    modalClass: '_image-box',
                    clickableOverlay: true,
                    buttons: [{
                        text: $.mage.__('Apply'),
                        class: '',
                        click: function () {
                            window.editorData($('#_versatile_editor').val());

                            this.closeModal();
                        }
                    }]
                }, $('#wysimyg_editor'));

                setTimeout(function () {
                    $("#toggle_versatile_editor").trigger("click").trigger("click");
                }, 2000);

                self.bindAction();

                return this;
            },

            /**
             * Init nest item
             * @param $data
             * @returns {*}
             */
            initNestItem: function ($data) {
                var self = this,
                    menuObj = new nest($data);

                menuObj.parentLevel1 = self;
                menuObj.initImageUploaderComponent();
                self.nestedMenu.push(menuObj);

                if ($data['childrenraw'] != undefined) {
                    $.each($data['childrenraw'], function (index, $node) {
                        menuObj.children.push(self.initNestItem($node));
                    });
                }

                return menuObj;
            },

            /**
             * Bind action
             */
            bindAction: function () {
                var self = this,
                    action = function (event, ac) {
                        var nestJson = $('.dd').nestable('serialize'),
                            editForm = $('#edit_form');

                        self.updateNestOrder();
                        self.updateRelation(nestJson);

                        ko.utils.arrayForEach(self.nestedMenu(), function (item) {
                            item.getSerializeChildren();
                            item.createData();
                        });

                        if (!_.isUndefined(ac)) {
                            if (ac === 'preview') {
                                return;
                            }

                            editForm.append($('<input />', {
                                name: 'action',
                                type: 'hidden',
                                value: ac
                            }));
                        }

                        editForm.submit();
                    };
                $('#savemenu').on('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    $('body').trigger('processStart');
                    action(event)
                });
                $('#saveandcontinue').on('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    $('body').trigger('processStart');
                    action(event, 'save_and_continue')
                });

                $('#preview').on('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    action(event, 'preview');
                    window.open('about:blank', 'myPage');

                    $.when($.ajax({
                        method: 'POST',
                        url: '' + self.params.previewUrl,
                        showLoader: true,
                        data: {
                            preview_data: JSON.stringify($('#edit_form').serializeArray())
                        }
                    })).done(function (response) {
                        if (response.error) {
                            alert(response.message);
                        } else {
                            window.open(self.params.baseUrl + '?mega_menu_preview=' + response.token, 'myPage');
                        }
                    });
                });
            },

            /**
             * Update relation
             *
             * @param nestJson
             */
            updateRelation: function (nestJson) {
                var self = this;

                if (nestJson.length > 0) {
                    ko.utils.arrayForEach(self.nestedMenu(), function (item) {
                        if (item === undefined) {
                            return;
                        }

                        var selector = 'li[data-id="$id"][data-level="0"] > ol > li'.replace('$id', item.id());
                        $(selector).remove();

                        //1 item.level(1);
                        item.levelTemp(1);
                        item.flatChildren.removeAll();
                        item.children.removeAll();
                    });

                    $.each(nestJson, function (index, obj) {
                        self.updateNodeItem(obj);
                        self.updateLevel(obj['id'], 0);
                    });
                }
            },

            isNewMenu: function () {
                return !window.megaMenuConfig.menu_id;
            },

            /**
             * Update level
             *
             * @param id
             * @param level
             */
            updateLevel: function (id, level) {
                for (var i = 0; i < this.nestedMenu().length; i++) {
                    var item = this.nestedMenu()[i];
                    if (item.id() == id) {
                        item.level(level);
                        item.levelTemp(level);
                        return level;
                    }
                }
            },

            /**
             * Update node Item
             *
             * @param nodeData
             */
            updateNodeItem: function (nodeData) {
                var self = this;
                var id = nodeData['id'];

                if (!_.isUndefined(nodeData['children'])) {
                    // Remove duplicate children
                    var allIds = [];
                    nodeData['children'] = nodeData['children'].filter(function (child) {
                        if (allIds.indexOf(child.id) === -1) {
                            allIds.push(child.id);
                            return true;
                        }
                        return false;
                    });

                    if (nodeData['children'].length) {
                        $.each(nodeData['children'], function (index, obj) {
                            self.addFlatChild(id, obj['id']);
                        });
                        //recursive
                        $.each(nodeData['children'], function (index, obj) {
                            self.updateNodeItem(obj);
                        });
                    }
                }
            },

            /**
             * Add last child
             *
             * @param id
             * @param childId
             */
            addFlatChild: function (id, childId) {
                var self = this;

                ko.utils.arrayForEach(self.nestedMenu(), function (item) {
                    if (item.id() == id) {
                        item.flatChildren.push(childId);
                        item.children.push(_.find(self.nestedMenu(), function (findItem) {
                            return findItem.id() == childId;
                        }));
                    }
                });
            },

            /**
             * Add selected page to array
             *
             * @param $page
             * @returns {boolean}
             */
            checkPage: function ($page) {
                var self = $page.parentLevel1,
                    name,
                    link;

                self.selectedPage([]);
                ko.utils.arrayForEach(self.chosenPages(), function (id) {
                    ko.utils.arrayForEach(self.pages(), function (pid) {
                        if (id == pid.page_id()) {
                            name = pid.title();
                            link = pid.link();
                        }
                    });
                    self.selectedPage.push({
                        id: 0,
                        name: name,
                        link: link,
                        parent: 0,
                        level: 0,
                        type: 'page',
                        obj_id: id
                    });
                });

                return true;
            },

            /**
             * Add selected pages
             *
             * @param data
             * @param event
             */
            btnAddPage: function (data, event) {
                var self = this,
                    menuIds = $.map(self.nestedMenu(), function (menu) {
                        return menu.id();
                    }),
                    maxId = (menuIds.length == 0) ? 1 : Math.max.apply(this, menuIds) + 1;

                ko.utils.arrayForEach(self.selectedPage(), function (page) {
                    var menuItem = new nest({
                        id: maxId,
                        is_new: true,
                        title: page.name,
                        link: page.link,
                        itemEnable: 1,
                        level: 0,
                        sort: 100,
                        hasParent: false,
                        type: page.type,
                        obj_id: page.obj_id,
                        menu_type: 1
                    });

                    menuItem.parentLevel1 = self;
                    menuItem.initImageUploaderComponent();
                    self.nestedMenu.push(menuItem);
                    maxId++;
                });

                self.selectedPage([]);
                self.chosenPages([]);
                self.sortItems();
            },

            /**
             * Sort items
             */
            sortItems: function () {
                var newNestedMenu = this.nestedMenu.sort(function (l, r) {
                    return parseInt(l.sort()) > parseInt(r.sort()) ? 1 : -1
                });
                newNestedMenu = ko.utils.unwrapObservable(newNestedMenu);

                this.nestedMenu(newNestedMenu);
            },

            /**
             * Add selected category to array
             *
             * @param $cat
             * @returns {boolean}
             */
            checkCat: function ($cat) {
                var self = $cat.parentLevel1,
                    name,
                    link;

                self.selectedCats().length = 0;

                ko.utils.arrayForEach(self.chosenCats(), function (id) {
                    ko.utils.arrayForEach(self.cats(), function (pid) {
                        if (id == pid.cat_id()) {
                            name = pid.title();
                            link = pid.link();
                        }
                    });

                    self.selectedCats.push({
                        id: 0,
                        name: name,
                        link: link,
                        parent: 0,
                        level: 0,
                        type: 'cat',
                        obj_id: id
                    });
                });

                return true;
            },

            /**
             * Add categories
             *
             * @param data
             * @param event
             */
            btnAddCat: function (data, event) {
                var self = this,
                    menuIds = $.map(self.nestedMenu(), function (menu) {
                        return menu.id();
                    }),
                    maxId = (menuIds.length == 0) ? 1 : Math.max.apply(this, menuIds) + 1;

                ko.utils.arrayForEach(self.selectedCats(), function (cat) {
                    var find = _.find(self.cats(), function (item) {
                        return item.cat_id() == cat.obj_id;
                    });

                    if (!find.visible())
                        return;

                    var newAddedCat = new nest({
                        id: maxId,
                        is_new: true,
                        title: cat.name,
                        itemEnable: 1,
                        link: cat.link,
                        level: 0,
                        sort: 100,
                        hasParent: false,
                        type: cat.type,
                        obj_id: cat.obj_id,
                        menu_type: 2
                    });


                    newAddedCat.parentLevel1 = self;
                    newAddedCat.initImageUploaderComponent();
                    self.nestedMenu.push(newAddedCat);
                    maxId++;
                });

                self.selectedCats([]);
                self.chosenCats([]);
                self.sortItems();
            },

            /**
             * Add custome Link
             *
             * @param data
             * @param event
             */
            btnCustomLink: function (data, event) {
                var self = this,
                    menuIds = $.map(self.nestedMenu(), function (menu) {
                        return menu.id();
                    }),
                    maxId = (menuIds.length == 0) ? 1 : Math.max.apply(this, menuIds) + 1,
                    linkMenu = new nest({
                        id: maxId,
                        is_new: true,
                        title: self.custom_link_title(),
                        level: 0,
                        itemEnable: 1,
                        sort: 100,
                        hasParent: false,
                        type: 'link',
                        obj_id: 0,
                        link: self.custom_link_link(),
                        menu_type: 3
                    });

                linkMenu.parentLevel1 = self;
                linkMenu.initImageUploaderComponent();
                self.nestedMenu.push(linkMenu);
                self.sortItems();
            },

            /**
             * Add custome Link
             *
             * @param data
             * @param event
             */
            classAddItem: function (data, event) {
                if ($('#nestedMenu').length) {
                    $('#nestedMenu').addClass('menu-settings-open');
                    $('.dd-list .dd-item .dd-handle').removeClass('active');
                }
            },

            /**
             * Update nest order
             */
            updateNestOrder: function () {
                var self = this;

                $('div.dd li').each(function (i, e) {
                    var idOfMenuItem = $(e).data('id');

                    ko.utils.arrayForEach(self.nestedMenu(), function (item) {
                        if (item.id() == idOfMenuItem) {
                            item.sort(i);
                        }
                    });
                });

                self.sortItems();
            }
        });

    }
);

