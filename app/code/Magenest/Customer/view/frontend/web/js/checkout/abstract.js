define([
    'Magento_Ui/js/form/element/abstract',
    'mage/translate'
], function (AbstractField, $t) {
    'use strict';

    return AbstractField.extend({
        defaults: {
            modules: {
                firstname: '${ $.parentName }.firstname',
                lastname: '${ $.parentName }.lastname',
            }
        },

        hasChanged: function () {
            this._super();
            this.setFirstLastNameFromFullName();
        },

        /**
         * Extract from the 'Full Name' the 'First' and 'Last' Name
         */
        setFirstLastNameFromFullName: function () {
            var fullValue = this.value();
            var lastSpacePos = fullValue.lastIndexOf(" ");
            var nameLimit = 50;
            var lastNameLimit = 50;

            if (lastSpacePos > 0) { // Ignore values with no space character
                if (fullValue.substring(0, lastSpacePos).length < nameLimit) {
                    // Update "firstName"
                    this.firstname().value(fullValue.substring(0, lastSpacePos));
                }
                if (fullValue.substring(lastSpacePos + 1).length < lastNameLimit) {
                    // Update "lastName"
                    this.lastname().value(fullValue.substring(lastSpacePos + 1));
                }
            } else {
                if (fullValue.length < nameLimit) {
                    // Update "firstName"
                    this.firstname().value(fullValue);
                }
            }
        },
    });
});
