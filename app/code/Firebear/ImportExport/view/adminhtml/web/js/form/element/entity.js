/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

define(
    [
        'jquery',
        'underscore',
        'mageUtils',
        'uiRegistry',
        'Firebear_ImportExport/js/form/element/additional-select',
        'uiLayout'
    ],
    function ($, _, utils, registry, Abstract, layout) {
        'use strict';

        return Abstract.extend(
            {
                defaults: {
                    code: '',
                    apiOptions: null
                },
                initialize: function () {
                    this._super();
                    var elements = this.getOption(this.value());
                    if (elements != undefined) {
                        this.setCode(elements.code);
                    }

                    return this;
                },
                initObservable: function () {
                    this._super();

                    this.observe('code');

                    return this;
                },
                setCode: function (value) {
                    this.code(value);
                    /* Hidding extra fields when entity is CART PRICE RULE (POSTBACK) */
                    var entityValue = value;
                    setTimeout(function () {
                        $('.admin__field').each(function () {
                            var dataIndex = $(this).data('index');
                            if (dataIndex == 'category_levels_separator') {
                                if (entityValue == 'cart_price_rule_behavior' || entityValue == 'order_behavior') {
                                    $(this).css('display', 'none');
                                } else {
                                    $(this).css('display', 'block');
                                }
                            }
                            if (dataIndex == 'categories_separator') {
                                if (entityValue == 'cart_price_rule_behavior' || entityValue == 'order_behavior') {
                                    $(this).css('display', 'none');
                                } else {
                                    $(this).css('display', 'block');
                                }
                            }
                        });
                    }, 3000);
                },

                onUpdate: function () {
                    this._super();
                    var map = registry.get(this.ns + '.' + this.ns + '.source_data_map_container.source_data_map');
                    var mapCategory = registry.get(this.ns + '.' + this.ns + '.source_data_map_container_category.source_data_categories_map');
                    var removeMapping = registry.get(this.ns + '.' + this.ns + '.source.remove_current_mappings');
                    if (removeMapping !== undefined && removeMapping.value() == 1) {
                        map.deleteRecords();
                    }
                    map._updateCollection();
                    mapCategory.deleteRecords();
                    mapCategory._updateCollection();
                    registry.get(this.ns + '.' + this.ns + '.source.check_button').showMap(0);
                    if (this.value() == undefined) {
                        this.setCode('');
                    } else {
                        var elements = this.getOption(this.value());
                        this.setCode(elements.code);
                    }
                    /* Update platform dropdown */
                    var platform = registry.get(this.ns + '.' + this.ns + '.settings.platforms');
                    if (this.value()) {
                        $.ajax({
                            url: this.loadPlatformUrl,
                            type: 'post',
                            dataType: 'json',
                            cache: false,
                            showLoader: true,
                            data: {entity: this.value()}
                        }).done(function (response) {
                            platform.options(response.options);
                        });
                    }
                    /* Hidding extra fields when entity CART PRICE RULE is selected */
                    var entityValue = this.value();
                    $('.admin__field').each(function () {
                        var dataIndex = $(this).data('index');
                        if (dataIndex == 'category_levels_separator') {
                            if (entityValue == 'cart_price_rule' || entityValue == 'order') {
                                $(this).css('display', 'none');
                            } else {
                                $(this).css('display', 'block');
                            }
                        }
                        if (dataIndex == 'categories_separator') {
                            if (entityValue == 'cart_price_rule' || entityValue == 'order') {
                                $(this).css('display', 'none');
                            } else {
                                $(this).css('display', 'block');
                            }
                        }
                    });
                }
            }
        );
    }
);
