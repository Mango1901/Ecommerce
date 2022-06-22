define([
    'jquery',
    'underscore',
    'mage/url',
], function ($, _, url) {
    'use strict';

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {
            _UpdatePrice: function () {
                this._super();
                var $widget = this,
                    options = _.object(_.keys($widget.optionsMap), {}),
                    productForm,
                    result;

                $widget.element.find('.' + $widget.options.classes.attributeClass + '[option-selected]').each(function () {
                    var attributeId = $(this).attr('attribute-id');

                    options[attributeId] = $(this).attr('option-selected');
                });
                productForm = $($widget.productForm.prevObject[0]);

                result = $widget.options.jsonConfig.optionPrices[_.findKey($widget.options.jsonConfig.index, options)];

                if (result != undefined) {
                    $.ajax({
                        method: "POST",
                        url: url.build('') + 'rewardpoints/product/getPointEarn',
                        data: {
                            final_price: result.finalPrice.amount,
                            product_id: $widget._determineProductData().productId
                        },
                        dataType: "json",
                        showLoader: true,
                    }).done(function (response) {
                        // update point in PDP
                        var finalPrice = $('.product-info-main').find('.price-box.price-final_price');
                        if (finalPrice.find('#point-show1').length) {
                            finalPrice.find('#point-show1').remove();
                        }
                        $('.product-info-main').find('.price-box.price-final_price .normal-price').append(response.point_earn);

                        // update point in category page
                        if (productForm.length) {
                            productForm.find('.price-box.point-box').html(response.point_earn);
                        }
                    });
                }
            }
        });

        return $.mage.SwatchRenderer;
    };
});