define([
    "jquery",
    "mage/template",
    "mage/translate",
    "mage/mage"
], function($) {
    'use strict';
    return function (config) {
        //Change small video display
        $(document).on("change", ".upload-video", function (e) {
            var idInputFile = $(this).attr("id"),
                prefix = idInputFile.substr(13),
                idVideoDisplay = '#video-preview-' + prefix,
                idWarning = '#message-warning-video-' + prefix;
            function readURL(input) {
                if (input.files && input.files[0]) {
                    //check photos are uploaded
                    var messageError = $("#error-type-file-video"),
                        arrayExtensions = ['mp4'],
                        file = input.files[0],
                        ext = file.name.split("."),
                        flag = true;
                    messageError.empty();
                    ext = ext[ext.length-1].toLowerCase();
                    if (arrayExtensions.lastIndexOf(ext) == -1) {
                        var message = $.mage.__("Only accept mp4 extension!");
                        messageError.append(message);
                        messageError.show();
                        $('#'+idInputFile).val("");
                        flag = false;
                    } else {
                        var inputs = e.target.files[0];
                        if(inputs && inputs.size > config.maxUploadSize){
                            var size = config.maxUploadSize/1048576;
                            var message = $.mage.__("Make sure your file isn't more than %1MB.").replace('%1', size);
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
                        $(idVideoDisplay).css('display', 'block');
                        $(idVideoDisplay).attr('src', '');
                        $(idVideoDisplay).attr('src', e.target.result);
                    };
                    if(flag){
                        reader.readAsDataURL(input.files[0]);
                        $("#upload_video_0").removeAttr("data-validate");
                    }else{
                        $(idWarning).css('display', 'none');
                        if(inputs && inputs.size > config.maxUploadSize){
                            $(idVideoDisplay).css('display', 'none');
                        }
                        else {
                            $(idVideoDisplay).css('display', 'block');
                        }
                        $(idVideoDisplay).attr('src', '');
                        $("#upload_video_0").attr("data-validate");
                    }

                }
            }
            readURL(this);
        });
    }
});
