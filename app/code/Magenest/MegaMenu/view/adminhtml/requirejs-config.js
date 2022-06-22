/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Ui/js/grid/filters/filters': {
                'Magenest_MegaMenu/js/model/grid/filters-mixin': true
            },
            'Magento_Ui/js/grid/filters/range': {
                'Magenest_MegaMenu/js/model/grid/range-mixin': true
            }
        }
    },
    map: {
        '*': {
            page: 'Magenest_MegaMenu/js/model/page',
            category: 'Magenest_MegaMenu/js/model/category',
            nest: 'Magenest_MegaMenu/js/model/nest'
        }
    },
    paths: {
        "colorpicker": "Magenest_MegaMenu/js/lib/colorpicker",
        "nestable": "Magenest_MegaMenu/js/lib/jquery.nestable"
    },
    shim: {
        'colorpicker': {
            'deps': ['jquery']
        },
        'nestable': {
            'deps': ['jquery']
        }
    }
};
try {
    // Since Magento 2.3.0, 'tinymce' has been replaced by 'tinymce4'
    // Check if 'tinymce4' is defined and put it to 'tinymce'
    // Should not use 'tinymce' alias, it's too common
    if (require.s.contexts._.config.map['*'].tinymce4) {
        config.map['*'].tinymce = require.s.contexts._.config.map['*'].tinymce4;
    }
    // else if (require.s.contexts._.config.paths.tinymce) {
    //     config.paths.mmTinymce = require.s.contexts._.config.paths.tinymce;
    // }
} catch (e) {
}
