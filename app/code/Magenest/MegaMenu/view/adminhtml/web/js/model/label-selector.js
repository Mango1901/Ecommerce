define([
    'Magento_Ui/js/form/element/select',
    './label'
], function (Element, label) {
    'use strict';

    return Element.extend({
        defaults: {
            listens: {
                value: 'setNestValue',
            }
        },
        initialize: function (args) {
            this._super(args);
            this.setOptions(label().getLabels());
            this.value(args.value);
        },
        setNestValue: function (v) {
            this.nest().label(v);
        }
    });
});