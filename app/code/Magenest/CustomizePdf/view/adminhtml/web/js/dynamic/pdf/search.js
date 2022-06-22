define([
    'jquery',
    'jquery/ui',
    'mage/mage',
    'mage/backend/suggest'
], function ($, ko, _, Component) {
    'use strict';

    $.widget('feed.search', $.mage.suggest, {
        options: {
            loadingClass: 'mage-suggest-state-loading',
            showRecent: true,
            showAll: true,
            delay: false,
        },

        _create: function () {
            this._superApply(arguments);

            this._on($.extend({
                change: function (event) {
                    var val = $(event.target).val();
                    this.valueField.val(val);
                }.bind(this)
            }))
        },

        _createValueField: function() {
            var $input = $('<input/>', {
                type: 'hidden'
            });

            $input.val(this.element.val());
            return $input;
        },
        /**
         * Bind handlers on specific events
         * @private
         */
        _bind: function () {
            this._on($.extend({
                /**
                 * @param {jQuery.Event} event
                 */
                keyup: function (event) {
                    var keyCode = $.ui.keyCode;

                    switch (event.keyCode) {
                        case keyCode.HOME:
                        case keyCode.END:
                        case keyCode.PAGE_UP:
                        case keyCode.PAGE_DOWN:
                        case keyCode.ESCAPE:
                        case keyCode.UP:
                        case keyCode.DOWN:
                        case keyCode.LEFT:
                        case keyCode.RIGHT:
                        case keyCode.TAB:
                            break;

                        case keyCode.ENTER:
                        case keyCode.NUMPAD_ENTER:
                            if (this.isDropdownShown()) {
                                event.preventDefault();
                            }
                            break;
                        default :
                            this.search(event);
                            break;
                    }
                },
                cut: this.search,
                paste: this.search,
                input: this.search,
                selectItem: this._onSelectItem,
                // click: this.search
            }, this.options.events));

            this._bindSubmit();
            this._bindDropdown();
        },
        _source: function (term, response) {
            if(term.length > 2){
                var o = this.options;

                if ($.isArray(o.source)) {
                    response(this.filter(o.source, term));
                } else if ($.type(o.source) === 'string') {
                    if (this._xhr) {
                        this._xhr.abort();
                    }
                    var parent = $(this.valueField).parent();
                    var ajaxData = {};
                    ajaxData[this.options.termAjaxArgument] = term;
                    $('.feed__dynamic-category-search', parent).show();

                    this._xhr = $.ajax($.extend(true, {
                        url: o.source,
                        type: 'POST',
                        dataType: 'json',
                        data: ajaxData,
                        success: $.proxy(function (items) {
                            $('.feed__dynamic-category-search', parent).hide();
                            this.options.data = items;
                            response.apply(response, arguments);
                        }, this)
                    }, o.ajaxOptions || {}));
                } else if ($.type(o.source) === 'function') {
                    o.source.apply(o.source, arguments);
                }
            }
        },


        setValue: function (val) {
            return $.trim(this.element[this.element.is(':input') ? 'val' : 'text'](val));
        },


        placeholder: function () {
            return $.trim(this.element.attr('placeholder'));
        }
    });

    return $.feed.search;

});
