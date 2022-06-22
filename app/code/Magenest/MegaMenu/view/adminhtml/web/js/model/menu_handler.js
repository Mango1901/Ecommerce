/**
 * Created by thuy on 24/05/2017.
 */

/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'ko',
        'colorpicker',
        'nestable'

    ],
    function ($, ko, mmmcorlor) {
        $(document).on("click", ".mega-fieldset-title", function () {
            var el = $(this).parent(".mega-fieldset");
            el.toggleClass("active");
            if (!el.hasClass("active")) {
                el.addClass("inactive");
            } else {
                el.removeClass("inactive");
            }
        });

        ko.bindingHandlers.visualmenubuilder = {
            /**
             * Scope binding's init method.
             * @returns {Object} - Knockout declaration for it to let binding control descendants.
             */
            init: function () {
                return {
                    controlsDescendantBindings: true
                };
            }
        };


        /**
         *
         * @type {{init: Function, update: Function}}
         */
        ko.bindingHandlers.mmfileupload = {
            init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
                $(element).fileupload({
                    dataType: 'json',
                    done: function (e, data) {
                        var imagePath = data.result.url;
                        viewModel.image(imagePath);
                    }
                });
            },
            update: function (element, valueAccessor, allBindings, viewModel, bindingContext) {

            }
        };

        /**
         *  panel
         * @type {{init: Function, update: Function}}
         */
        ko.bindingHandlers.panel = {
            init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
                $(element).find('.panel-title').on('click', function (e) {
                    $(element).find('.panel-content').slideToggle();
                });
            }
        };

        /**
         *
         * @type {{init: Function}}
         */
        ko.bindingHandlers.mmaccordion = {
            /**
             * Scope binding's init method.
             * @returns {Object} - Knockout declaration for it to let binding control descendants.
             */
            init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
                var icons = {
                    header: "icon-arrow-up",
                    activeHeader: "icon-arrow-down"
                };
                $(element).accordion({
                    active: true,
                    collapsible: true,
                    heightStyle: "content",
                    icons: icons
                });
            }
        };

        /**
         *
         * @type {{init: Function}}
         */
        ko.bindingHandlers.mmsortable = {

            /**
             * Scope binding's init method.
             * @returns {Object} - Knockout declaration for it to let binding control descendants.
             */
            init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {

                $(element).sortable({
                    placeholder: "sortable-placeholder",
                    start: function (e, ui) {
                        ui.placeholder.height(ui.item.height()).width(ui.item.width());
                        var menuId = ui.item.data('id');

                        var nextElement = ui.item.prev();


                        ko.utils.arrayForEach(viewModel.menus(), function (item) {
                            if (item.id() == menuId) {
                                var menuObj = item;

                                ///

                                ui.helper.append($('li[move="1"]'));
                            }
                        });

                        //ui.helper.append(nextElement.clone());
                    },

                    change: function (event, ui) {

                    },
                    beforeStop: function (event, ui) {


                    }

                });
            }
        };

        /**
         *
         * @type {{init: Function}}
         */
        ko.bindingHandlers.mmcolorpicker = {

            /**
             * Scope binding's init method.
             * @returns {Object} - Knockout declaration for it to let binding control descendants.
             */
            init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
                var options = {
                    color: $(element).val(),
                    onChange: function (hsb, hex, rgb, el, parent) {
                        $(element).val('#' + hex);
                        $(element).trigger('change');
                    }
                };
                $(element).on('keyup', function (e) {
                    if ([8, 46].indexOf(e.keyCode) + ['Backspace', 'Delete'].indexOf(e.key) > -2) {
                        $(element).val('');
                        $(element).trigger('change');
                    }
                });
                $(element).ColorPicker(options);
            }
        };

        /**
         *
         * @type {{init: Function}}
         */
        ko.bindingHandlers.mmnestable = {
            /**
             * Scope binding's init method.
             * @returns {Object} - Knockout declaration for it to let binding control descendants.
             */
            init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
                var accessor = ko.unwrap(valueAccessor());
                var updateLevelCallback = accessor.updateLevelCallback;
                $(element).nestable({
                    onDragFinished: function (e) {
                        var level = $(e).parents().eq(2).attr('id') === 'menu-structure' ? 0 : 1,
                            id = $(e).data('id');
                        updateLevelCallback(id, level);
                    }
                });
                $(element).nestable('collapseAll');
            }
        };

        ko.bindingHandlers.numberRange = {
            init: function (element, valueAccessor, allBindings) {
                var args = valueAccessor(),
                    valBinding = allBindings.get('value');
                valBinding.subscribe(function (newVal) {
                    if (newVal < args.min) {
                        valBinding(args.min);
                    } else if (newVal > args.max) {
                        valBinding(args.max);
                    }
                });
            }
        };

        /**
         *
         * @type {{init: Function}}
         */
        ko.bindingHandlers.mmdragdrop = {

            /**
             * Scope binding's init method.
             * @returns {Object} - Knockout declaration for it to let binding control descendants.
             */
            init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
                $(element).draggable({

                    revert: function (dropped) {
                        return true;

                    }
                });
            }
        };

        /**
         *
         * @type {{init: Function}}
         */
        ko.bindingHandlers.mmdrop = {

            /**
             * Scope binding's init method.
             * @returns {Object} - Knockout declaration for it to let binding control descendants.
             */
            init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
                $(element).draggable({

                    revert: function (dropped) {
                        return true;

                    }
                });
            }
        };

        ko.bindingHandlers.nestMe = {
            update: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
                var title;
                var value = ko.utils.unwrapObservable(valueAccessor()) || {};

                var level;
                ko.utils.objectForEach(value, function (attrName, attrValue) {
                    attrValue = ko.utils.unwrapObservable(attrValue);


                    if (attrName === "name") {
                        title = attrValue;
                    }
                    if (attrName === "level") {
                        level = attrValue;
                    }
                });


                element.innerHTML = '<li class="dd-item">' + title + " leve " + level +
                    '<ol class="dd-list" data-bind="foreach: children">' +
                    '<!--ko nestMe: {name:name,level:level}-->' +
                    '<div>title </div>' +
                    '<!--/ko-->' +
                    '</ol>' +
                    '</li>';

            }
        };
        ko.virtualElements.allowedBindings.nestMe = true;

    }
);
