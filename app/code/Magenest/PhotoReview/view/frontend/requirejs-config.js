var config = {
    map: {
        '*': {
            'slick': 'Magenest_PhotoReview/js/slick',
            'validation': 'mage/validation/validation'
        }
    },
    path: {
        'slick': 'Magenest_PhotoReview/js/slick'
    },
    shim:{
        'slick': {
            deps: ['jquery']
        }
    }
};
require.config(config);
