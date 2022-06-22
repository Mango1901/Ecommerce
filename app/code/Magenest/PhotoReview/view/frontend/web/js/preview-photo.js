define([
    "jquery",
    "mage/template",
    "mage/translate",
    "mage/mage"
], function($) {
    'use strict';
    return function (config) {
        //Change small image display
        $(document).on("change", ".upload-photo", function (e) {
            var idInputFile = $(this).attr("id"),
                prefix = idInputFile.substr(13),
                idImgDisplay = '#photo-preview-' + prefix,
                idWarning = '#message-warning-' + prefix,
                indexDataNext = $(this).attr("data-next"),
                indexDataCurrent = indexDataNext - 1;
            function readURL(input) {
                if (input.files && input.files[0]) {
                    //check photos are uploaded
                    var messageError = $("#error-type-file"),
                        arrayExtensions = ['jpg', 'jpeg', 'gif', 'png'],
                        file = input.files[0],
                        ext = file.name.split("."),
                        flag = true;
                    messageError.empty();
                    ext = ext[ext.length-1].toLowerCase();
                    if (arrayExtensions.lastIndexOf(ext) == -1) {
                        var message = $.mage.__("Only accept png, jpeg, jpg, gif extension!");
                        messageError.append(message);
                        messageError.show();
                        $('#'+idInputFile).val("");
                        flag = false;
                    } else {
                        var inputs = e.target.files[0];
                        if(inputs && inputs.size > config.maxUploadSize){
                            var message = $.mage.__("Make sure your file isn't more than {size}M.").replace('{size}', config.maxUploadSize);
                            messageError.append(message);
                            $('#'+idInputFile).val("");
                            flag = false;
                        }else{
                            messageError.empty();
                        }

                    }
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $(idWarning).css('display', 'none');
                        $(idImgDisplay).css('display', 'block');
                        $(idImgDisplay).attr('src', '');
                        $(idImgDisplay).attr('src', e.target.result);
                    };
                    if (flag) {
                        reader.readAsDataURL(input.files[0]);
                        $("#upload_photo_0").removeAttr("data-validate");
                        $('#label-' + indexDataCurrent).hide();
                        $('#label-' + indexDataNext).show();
                    } else {
                        $(idWarning).css('display', 'none');
                        $(idImgDisplay).css('display', 'block');
                        $(idImgDisplay).attr('src', '');
                        $("#upload_photo_0").attr("data-validate");
                    }

                }
            }
            readURL(this);
        });
    }
});
