define([
    'Magento_Ui/js/form/element/file-uploader',
    'ko',
    'mage/url'
], function (FileUploader, ko, urlBuilder) {
    'use strict';

    return FileUploader.extend({
        defaults: {
            elementTmpl: 'ui/form/element/uploader/uploader',
            template: 'ui/form/element/uploader/uploader',
            uploaderConfig: {
                url: window.megaMenuConfig.uploadUrl
            },
            previewTmpl: 'Magento_Catalog/image-preview',
            lineItemImage: ko.observable({}),
            maxFileSize: 20971520
        },

        addFile: function (file) {
            this.lineItemImage(file);
            return this._super(file);
        },

        removeFile: function (file) {
            this.lineItemImage({});
            return this._super(file);
        }
    });
});