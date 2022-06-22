define([
    'Magento_Ui/js/form/element/ui-select',
    'ko',
    'jquery',
    'mage/url'
], function (UiSelect, ko, $, urlBuilder) {
    'use strict';

    return UiSelect.extend({
        defaults: {
            "template": 'ui/grid/filters/elements/ui-select',
            "filterOptions": true,
            "multiple": false,
            "showCheckbox": false,
        },

        initialize: function(){
            this.$index = ko.observable(0);
            this._super();
        },

        /**
         * Toggle activity list element
         *
         * @param {Object} data - selected option data
         * @returns {Object} Chainable
         */
        toggleOptionSelected: function (data) {
            var self = this;
            var isSelected = this.isSelected(data.value);

            if (this.lastSelectable && data.hasOwnProperty(this.separator)) {
                return this;
            }

            if (!this.multiple) {
                if (!isSelected) {
                    this.categoryId(data.value);
                    $.ajax({
                        url: self.getCategoryInfoUrl,
                        data: {
                            categoryId: data.value
                        },
                        type: 'post',
                        dataType: 'json',
                        context: this,
                        success: function (response) {
                            if (response.status === true){
                                this.imgCateUrl(response.imageUrl);
                                this.categoryTitle(response.title);
                                this.categoryDesc(response.desc);
                                this.categoryUrl(response.url);
                            }
                        },
                    });

                }
            }

            return this._super(data);
        },
    });
});