define([
    'mage/utils/wrapper',
    'jquery'
], function (wrapper, $) {
    'use strict';

    return function (stepNavigator) {
        stepNavigator.stepNavigator = false;
        stepNavigator.handleHash = wrapper.wrapSuper(stepNavigator.handleHash, function () {
            this._super();
            if (this.previousClass) {
                $('body').removeClass(this.previousClass);
            }
            var hashString = window.location.hash.replace('#', '');

            if (this.previousClass == "payment") {
                $('.modal-popup button.action-close').click();
            }
            if (hashString === '') {
                this.previousClass = "shipping";
            } else {
                this.previousClass = hashString;
            }
            $('body').addClass(this.previousClass);
            // add extended functionality here or modify method logic altogether
        });

        return stepNavigator;
    };
});
