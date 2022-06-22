define([
    'underscore',
    'jquery',
    'ko',
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract'
], function (_, $, ko, uiRegistry, Abstract) {
    'use strict';
    return Abstract.extend({
        defaults: {
            isDisplay: false,
            categoryData: '',
            productData: '',

            image_url: '',
            text: '',
            position: 'top-left',
            text_size: '16px',
            label_text_left: '0',
            label_text_top: '0',
            text_color: '#000000',

            type: '',

            label_size: '80px',
            size: '',
            label_height: '',
            label_width: '',

            image: '',
            shape_type: 'shape-new-1',
            shape_color: '#000000',

            /*shape color style list*/
            background_color: '',
            border_color: '',
            border_width: '',
            myStyle: '',
            label_type: '',

            //Initialize the variables for the category page view  when clicking the label type field
            shape_type_virtual_category: '',
            text_virtual_category: '',
            only_virtual_category: false,
            assign_text_virtual_category: false,
            image_virtual_category: '',

            //Initialize the variables for the product page view  when clicking the label type field
            shape_type_virtual_product: '',
            text_virtual_product: '',
            only_virtual_product: false,
            assign_text_virtual_product: false,
            image_virtual_product: '',
            shape_default: 'shape-new-1',

            listens: {
                'name=label_form.areas.category:active': 'categoryTabActive',
                'name=label_form.areas.product:active': 'productTabActive',

                'name=label_form.areas.category.category.type:value': 'changeTypeValueCategory',
                'name=label_form.areas.product.product.type:value': 'changeTypeValueProduct',

                'name=label_form.areas.category.category.image:value': 'changeImageValue',
                'name=label_form.areas.product.product.image:value': 'changeImageValue',

                'name=label_form.areas.category.category.position:value': 'changePositionValue',
                'name=label_form.areas.product.product.position:value': 'changePositionValue',

                'name=label_form.areas.category.category.label_size:value': 'changeLabelSizeValue',
                'name=label_form.areas.product.product.label_size:value': 'changeLabelSizeValue',

                'name=label_form.areas.category.category.text:value': 'changeTextValue',
                'name=label_form.areas.product.product.text:value': 'changeTextValue',

                'name=label_form.areas.category.category.text_size:value': 'changeTextSizeValue',
                'name=label_form.areas.product.product.text_size:value': 'changeTextSizeValue',

                'name=label_form.areas.category.category.text_color:value': 'changeTextColorValue',
                'name=label_form.areas.product.product.text_color:value': 'changeTextColorValue',

                'name=label_form.areas.category.category.label_text_left:value': 'changeTextLeft',
                'name=label_form.areas.product.product.label_text_left:value': 'changeTextLeft',

                'name=label_form.areas.category.category.label_text_top:value': 'changeTextTop',
                'name=label_form.areas.product.product.label_text_top:value': 'changeTextTop',

                'name=label_form.areas.category.category.shape_type:value': 'changeShapeValue',
                'name=label_form.areas.product.product.shape_type:value': 'changeShapeValue',

                'name=label_form.areas.category.category.shape_color:value': 'changeShapeColorValue',
                'name=label_form.areas.product.product.shape_color:value': 'changeShapeColorValue',

                'name=label_form.areas.category.category.custom_css:value': 'changeCustomCssValue',
                'name=label_form.areas.product.product.custom_css:value': 'changeCustomCssValue',

                'name=label_form.areas.product.product.use_default:value': 'useDefaultValue'
            },
        },

        initialize: function () {
            this._super();
            return this;
        },

        initObservable: function () {
            this._super().observe([
                'isDisplay',
                'categoryData',
                'productData',

                'label_size',
                'position',
                'image_url',
                'text',
                'text_size',
                'label_text_left',
                'label_text_top',

                'type',
                'text_color',
                'image',
                'shape_type',
                'shape_color',

                'label_height',
                'label_width',

                'background_color',
                'border_color',
                'border_width',
                'myStyle',
                'shape_default',
                'label_type',

                'shape_type_virtual_category',
                'text_virtual_category',
                'only_virtual_category',
                'assign_text_virtual_category',
                'image_virtual_category',

                'shape_type_virtual_product',
                'text_virtual_product',
                'only_virtual_product',
                'assign_text_virtual_product',
                'image_virtual_product',
            ]);
            return this;
        },

        categoryTabActive: function (isActive, isChange) {
            var self = this;
            var data = self.data.category_data;
            var categoryTab = uiRegistry.get('name=label_form.areas.category');
            var productTab = uiRegistry.get('name=label_form.areas.product');
            if(isActive == true) {
                this.productTabActive(false, true);
                $('.product_label').show();
                self.isDisplay(true);
                if (data.type == 1 || data.type == 2){
                    data.image_url = '';
                }
                self.label_size(data.label_size.replace('px', '') + 'px');
                self.type(data.type);
                self.position(data.position);
                self.image_url(data.image_url);
                //1 -> label type = text only
                if (data.type == 1 || data.type == 2) {
                    self.text(data.text);
                } else {
                    self.text(null);
                }
                self.text_size(data.text_size.replace('px', '') + 'px');
                self.text_color(data.text_color);
                self.label_text_left(data.label_text_left);
                self.label_text_top(data.label_text_top);
                self.image(data.image);
                if (data.type == 2) {
                    self.shape_type(data.shape_type);
                } else {
                    self.shape_type(null);
                }
                self.shape_color(data.shape_color);
                self.myStyle(data.custome_css);
                self.shapeColorHandler(self.shape_color());
                self.handleLabelSize();
            }
            if(isChange && categoryTab.active()) {
                data.label_size = self.label_size();
                data.type = self.type();
                data.position = self.position();
                data.type = self.type();
                data.image_url = self.image_url();
                data.text = self.text();
                data.text_size = self.text_size();
                data.text_color = self.text_color();
                data.label_text_left = self.label_text_left();
                data.label_text_top = self.label_text_top();
                data.image = self.image();
                data.shape_type = self.shape_type();
                data.shape_color = self.shape_color();
                data.custome_css = self.myStyle;
            }
            if(!isActive && !productTab.active()) {
                self.isDisplay(false);
                $('.product_label').hide();
            }
        },

        productTabActive:function (isActive, isChange) {
            var self = this;
            var data = self.data.product_data;
            var useDefault = uiRegistry.get('name=label_form.areas.product.product.use_default').value();
            var productTab = uiRegistry.get('name=label_form.areas.product');
            var categoryTab = uiRegistry.get('name=label_form.areas.category');
            var labelType = self.label_type();
            if(isActive == true ) {
                if(useDefault == 1) data = self.data.category_data;
                this.categoryTabActive(false, true);
                $('.product_label').show();
                self.isDisplay(true);
                //1 => Text Only; 2 => Shape; 3 => Image
                if (data.type == 1 || data.type == 2){
                    data.image_url = '';
                } else if (data.type == 3 || labelType == 3) {
                    //Hide text fields when selecting Label Type = Image (when edit label)
                    checkShowTextField(productTab)
                }
                self.label_size(data.label_size.replace('px', '') + 'px');
                self.type(data.type);
                self.position(data.position);
                self.image_url(data.image_url);
                //1 -> label type = text only
                if (data.type == 1 || data.type == 2) {
                    self.text(data.text);
                } else {
                    self.text(null);
                }
                self.text_size(data.text_size.replace('px', '') + 'px');
                self.text_color(data.text_color);
                self.label_text_left(data.label_text_left);
                self.label_text_top(data.label_text_top);
                self.image(data.image);
                //2 -> label type = image
                if (data.type == 2) {
                    self.shape_type(data.shape_type);
                } else {
                    self.shape_type(null);
                }
                self.shape_color(data.shape_color);
                self.myStyle(data.custom_css);
                self.shapeColorHandler(self.shape_color());
                self.handleLabelSize();
                checkShowLabelPreview(useDefault);
            }
            if(isChange && productTab.active() && (useDefault == 0)) {
                data.label_size = self.label_size();
                data.type = self.type();
                data.position = self.position();
                data.type = self.type();
                data.image_url = self.image_url();
                data.text = self.text();
                data.text_size = self.text_size();
                data.text_color = self.text_color();
                data.label_text_left = self.label_text_left();
                data.label_text_top = self.label_text_top();
                data.image = self.image();
                data.shape_type = self.shape_type();
                data.shape_color = self.shape_color();
                data.custom_css = self.myStyle();
            }

            if(!isActive && !categoryTab.active()) {
                self.isDisplay(false);
                $('.product_label').hide();
            }
        },

        /*init container size of label*/
        handleLabelSize: function () {
            var self = this;
            self.size = self.label_size();
            self.label_height(self.size + 'px');
            self.label_width(self.size + 'px');

            var diagonalSize = Math.round(self.size / 2 * Math.sqrt(2));
            var applySizeString = diagonalSize + 'px ';
            switch (self.shape_type()) {
                case "":
                    self.border_width('');
                    break;
                case "0":
                    self.border_width('');
                    break;
                case "1":
                    self.border_width('');
                    break;
                case "2":
                case "3":
                    self.border_width('0 ' + applySizeString + applySizeString + applySizeString);
                    break;
                case "4":
                case "5":
                    self.border_width(applySizeString + applySizeString + '0 ' + applySizeString);
                    break;
                case "6":
                    self.border_width('');
                    break;
                case "7":
                case "8":
                    self.border_width('');
                    break;
                default:
                    self.border_width('');
                    break;
            }
        },

        changeShapeValue: function (value) {
            var self = this;
            var categoryTab = uiRegistry.get('name=label_form.areas.category');
            var dataCategory = self.data.category_data;
            var dataProduct = self.data.product_data;
            self.shape_type(value);
            if (categoryTab.active()) {
                dataCategory.shape_type = value;
            } else {
                dataProduct.shape_type = value;
            }
            if (value != '0') {
                self.image_url(null);
                var data = self.shape_color();
                if (data != null) {
                    self.shapeColorHandler(self.shape_color());
                    self.handleLabelSize();
                } else {
                    var values = self.shape_color('#000000');
                    var color = values.shape_color();
                    self.shapeColorHandler(color);
                    self.handleLabelSize();
                }
            }
        },

        shapeColorHandler: function (value) {
            var self = this;
            var left = self.label_text_left() + '%';
            var top = self.label_text_top() + '%';

            switch (self.shape_type()) {
                case "":
                    self.border_color('');
                    self.background_color('');
                    break;
                case "0":
                    var style = '.image-text { left: ' + left + ';' +
                        'top: ' + top + ';' +
                        'transform: translate(-' + left + ', -' + top + ')' + '; } ';

                    self.border_color('');
                    self.background_color('');
                    self.myStyle(style);
                    break;
                case "1":
                    self.border_color('');
                    self.background_color(value);
                    break;
                case "2":
                case "3":
                    self.border_color('transparent transparent ' + value + ' transparent');
                    self.background_color('');
                    break;
                case "4":
                case "5":
                    self.border_color(value + ' transparent transparent transparent');
                    self.background_color('');
                    break;
                case "6":
                    self.border_color('');
                    self.background_color(value);
                    var size = self.size / (Math.sin(60 * Math.PI / 180.0) / (1 + Math.sin(30 * Math.PI / 180.0) + Math.cos(30 * Math.PI / 180.0)) + 1);
                    var star_size = Math.round(size) + 'px';
                    self.myStyle('.twelve-point-star { background-color: ' + self.background_color() + ';' +
                        'height: ' + star_size + ';' +
                        'width: ' + star_size + '; } '
                        + '.twelve-point-star:before { background-color: ' + self.background_color() + ';' +
                        'height: ' + star_size + ';' +
                        'width: ' + star_size + '; } '
                        + '.twelve-point-star:after { background-color: ' + self.background_color() + ';' +
                        'height: ' + star_size + ';' +
                        'width: ' + star_size + '; } '
                    );
                    break;
                case "7":
                case "8":
                    self.border_color('');
                    self.background_color(value);
                    break;
                default:
                    self.border_color('');
                    self.background_color('');
            }
        },

        /*handler of component change*/
        changeShapeColorValue: function (value) {
            var self = this;
            self.shape_color(value);
            self.shapeColorHandler(value);
        },

        changeImageValue: function (value) {
            var self = this;
            if (Array.isArray(value)) {
                if (value[0]) {
                    var categoryTab = uiRegistry.get('name=label_form.areas.category');
                    var dataCategory = self.data.category_data;
                    var dataProduct = self.data.product_data;

                    self.image_url(value[0].url);
                    if (categoryTab.active()) {
                        dataCategory.image_url = value[0].url;
                        //3 -> label type image
                        dataCategory.type = '3';
                    } else {
                        dataProduct.image_url = value[0].url;
                        //3 -> label type image
                        dataProduct.type = '3';
                    }

                    /*handle no shape when image upload*/
                    if (self.image('value')){
                        self.shape_type(false);
                    }
                    else {
                        self.image(null);
                        self.shape_type(false);
                        self.shape_color('');
                    }
                }
                else {
                    self.image_url(null);
                }
            }
        },
        changePositionValue:function (value) {
            var self= this;
            self.position(value);
        },
        changeLabelSizeValue: function (value) {
            var self = this;
            self.label_size(value + 'px');
            self.handleLabelSize();
        },
        changeTextValue: function (value) {
            var self = this;
            var productTab = uiRegistry.get('name=label_form.areas.product');
            var dataCategory = self.data.category_data;
            var dataProduct = self.data.product_data;
            self.text(value);
            if (productTab.active()) {
                dataProduct.text = value;
            } else {
                dataCategory.text = value;
            }
        },
        changeTextSizeValue: function (value) {
            var self = this;
            if (value == '') {
                value = '16';
            }
            var textSizeValue = value + 'px';
            self.text_size(textSizeValue);
        },
        changeTextColorValue: function (value) {
            var self = this;
            self.text_color(value);
        },

        changeTextLeft: function (value) {
            var self = this;
            self.label_text_left(value);
            self.shapeColorHandler(self.shape_color());
        },

        changeTextTop: function (value) {
            var self = this;
            self.label_text_top(value);
            self.shapeColorHandler(self.shape_color());
        },

        changeCustomCssValue: function (value) {
            var self = this;
            self.myStyle(value);
        },

        useDefaultValue: function (value) {
            var self = this;
            var data = self.data.product_data;
            var productTab = uiRegistry.get('name=label_form.areas.product');
            var labelType = self.label_type();
            if(value == 1) data = self.data.category_data;

            self.isDisplay(true);
            if (data.type == 1 || data.type == 2){
                data.image_url = '';
            }
            //Check Label Type = Image
            if (data.type == 3 || labelType == 3) {
                checkShowTextField(productTab);
            }
            self.label_size(data.label_size.replace('px', '') + 'px');
            self.type(data.type);
            self.position(data.position);
            self.image_url(data.image_url);
            self.text(data.text);
            self.text_size(data.text_size.replace('px', '') + 'px');
            self.text_color(data.text_color);
            self.label_text_left(data.label_text_left);
            self.label_text_top(data.label_text_top);
            self.image(data.image);
            self.shape_type(data.shape_type);
            self.shape_color(data.shape_color);
            self.shapeColorHandler(self.shape_color());
            self.handleLabelSize();
            checkShowLabelPreview(value)
        },

        //Category Page View
        changeTypeValueCategory: function (value) {
            var self = this;
            self.type(value);
            var categoryTab = uiRegistry.get('name=label_form.areas.category');
            var dataCategory = self.data.category_data;

            if (categoryTab.active()) {
                //1->click: text only, 2->click: shape, 3->click: Image
                if (value == 1) {
                    if (!self.assign_text_virtual_category()) {
                        self.shape_type_virtual_category(self.shape_type());
                    }
                    dataCategory.type = value;
                    if (self.text() == null) {
                        self.text(self.text_virtual_category());
                    }
                    self.shape_type(false);
                    self.only_virtual_category(true);
                    if (!self.image_virtual_category()) {
                        self.image_virtual_category(self.image_url());
                    }
                    self.image_url(false);
                } else if (value == 2) {
                    // var dataCategory = self.data.category_data;
                    if (dataCategory.shape_type == '') {
                        dataCategory.shape_type = self.shape_default();
                    }
                    dataCategory.type = value;
                    var shape = self.shape_type();
                    var text = self.text();
                    var labelId = self.data.label_id;
                    var shapeTypeVirtual = self.shape_type_virtual_category();
                    var dataShapeType = self.data.category_data.shape_type;
                    if (!shape && shape !== '' && shapeTypeVirtual != '') {
                        //Get shape_type from the virtual variable created earlier
                        self.shape_type(shapeTypeVirtual);
                        if (text == null) {
                            self.text(self.text_virtual_category());
                        }
                    } else {
                        //Show shape type preview default
                        self.shape_type(self.shape_default());
                    }
                    if (!self.image_virtual_category()) {
                        self.image_virtual_category(self.image_url());
                    }

                    //Show shape type default when the edit label
                    if (labelId && dataShapeType == null && !shapeTypeVirtual) {
                        self.shape_type(self.shape_default());
                    }

                    self.image_url(false);
                } else if (value == 3) {
                    self.text_virtual_category(self.text());
                    dataCategory.type = value;
                    if (self.shape_type()) {
                        //Assign shape_type to the virtual variable
                        self.shape_type_virtual_category(self.shape_type());
                    }
                    if (!self.image_url()) {
                        self.image_url(self.image_virtual_category());
                        dataCategory.image_url = self.image_virtual_category();
                        self.image_virtual_category(false);
                    }
                    if (self.shape_type() != '') {
                        self.shape_type(false);
                    }
                    self.assign_text_virtual_category(true);
                    self.text(null);
                }
            }
        },

        //Product Page View
        changeTypeValueProduct: function (value) {
            var self = this;
            self.type(value);
            self.label_type(value);
            var productTab = uiRegistry.get('name=label_form.areas.product');
            var dataProduct = self.data.product_data;

            if (productTab.active()) {
                if (value == 1) {
                    if (!self.assign_text_virtual_product()) {
                        self.shape_type_virtual_product(self.shape_type());
                    }
                    if (self.text() == null) {
                        self.text(self.text_virtual_product());
                    }
                    dataProduct.type = value;
                    self.shape_type(false);
                    self.only_virtual_product(true);
                    if (!self.image_virtual_product()) {
                        self.image_virtual_product(self.image_url());
                    }
                    self.image_url(false);
                } else if (value == 2) {
                    // var dataProduct = self.data.product_data;
                    if (dataProduct.shape_type == '') {
                        dataProduct.shape_type = self.shape_default();
                    }
                    dataProduct.type = value;
                    var shape = self.shape_type();
                    var text = self.text();
                    var labelId = self.data.label_id;
                    var shapeTypeVirtual = self.shape_type_virtual_product();
                    var dataShapeType = self.data.category_data.shape_type;
                    if (!shape && shape !== '' && shapeTypeVirtual != '') {
                        //Get shape_type from the virtual variable created earlier
                        self.shape_type(shapeTypeVirtual);
                        if (text == null) {
                            self.text(self.text_virtual_product());
                        }
                    } else {
                        //Show shape type preview default
                        self.shape_type(self.shape_default());
                    }
                    if (!self.image_virtual_product()) {
                        self.image_virtual_product(self.image_url());
                    }

                    //Show shape type default when the edit label
                    if (labelId && dataShapeType == null && !shapeTypeVirtual || !dataShapeType) {
                        self.shape_type(self.shape_default());
                    }

                    self.image_url(false);
                } else if (value == 3) {
                    self.text_virtual_product(self.text());
                    if (self.shape_type()) {
                        //Assign shape_type to the virtual variable
                        self.shape_type_virtual_product(self.shape_type());
                    }
                    if (!self.image_url()) {
                        self.image_url(self.image_virtual_product());
                        dataProduct.image_url = self.image_virtual_category();
                        self.image_virtual_product(false);
                    }
                    if (self.shape_type() != '') {
                        self.shape_type(false);
                    }
                    dataProduct.type = value;
                    self.assign_text_virtual_product(true);
                    self.text(null);
                }
            }
        }
    });

    function checkShowLabelPreview(value) {
        if (value == '1') {
            $('.product_label').hide();
        } else {
            $('.product_label').show();
        }
    }

    //Hide text fields when selecting Label Type = Image (when edit label)
    function checkShowTextField(productTab) {
        if (productTab.active()) {
            uiRegistry.get('name=label_form.areas.product.product.text').hide();
            uiRegistry.get('name=label_form.areas.product.product.text_color').hide();
            uiRegistry.get('name=label_form.areas.product.product.text_size').hide();
        }
    }
});
