define([
    'Magento_Ui/js/form/element/date'
], function (dateComponent) {
    return dateComponent.extend({
        defaults: {
            template: "ui/form/element/date",
            options: {
                pickerDefaultDateFormat: "y-MM-dd",
                dateFormat: "y-MM-dd",
                outputDateFormat: "y-MM-dd"
            },
            outputDateTimeToISO: false
        }
    });
});
