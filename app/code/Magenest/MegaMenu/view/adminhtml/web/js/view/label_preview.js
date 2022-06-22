require([
    'jquery',
    "underscore",
    'jquery/colorpicker/js/colorpicker'
], function ($, _) {
    'use strict';
    $(function () {
        // $('input[type="number"]')
        //     .prop('step', 0.1)
        //     .on("input", function (event) {
        //         event.currentTarget.value = event.currentTarget.valueAsNumber;
        //         // if (!/^[0-9]{1}$/.test(event.key) && event.keyCode !== 8) {
        //         //     event.preventDefault();
        //         // }
        //     });
        var $preview = $("#megamenu-label");
        var $text = $("#mm_label_text");
        var $position = $("#mm_label_position");
        var $fontSize = $("#mm_label_font-size");
        var $he = $("#mm_label_height");
        var $wi = $("#mm_label_width");
        var $ta = $("#mm_label_text-align");
        var $tc = $("#mm_label_color");
        var $bc = $("#mm_label_background-color");
        var $borderWidth = $("#mm_label_border-width");
        var $borderStyle = $("#mm_label_border-style");
        var $borderColor = $("#mm_label_border-color");
        var $borderRadius = $("#mm_label_border-radius");
        var $arrow = $("#mm_label_arrow");
        var $arrowWidth = $("#mm_label_arrow-border-width");
        var $arrowColor = $("#mm_label_arrow-border-color");
        $tc.css("backgroundColor", $tc.val());
        $bc.css("backgroundColor", $bc.val());
        $borderColor.css("backgroundColor", $borderColor.val());
        $arrowColor.css("backgroundColor", $arrowColor.val());

        var $to_html = $('#mm_label_to_html');

        $text.on("change", function () {
            $preview.children('.label-text').html($text.val());
            toHtml();
        });

        $position.on("change", function () {

            /* Remove Class begins with label-position- */
            $preview.removeClass(function (index, classNames) {
                var current_classes = classNames.split(" "), // change the list into an array
                    classes_to_remove = []; // array of classes which are to be removed
                $.each(current_classes, function (index, class_name) {
                    // if the classname begins with label-position- add it to the classes_to_remove array
                    if (/label-position-.*/.test(class_name)) {
                        classes_to_remove.push(class_name);
                    }
                });
                // turn the array back into a string
                return classes_to_remove.join(" ");
            });

            $preview.addClass($position.val());
            toHtml();
        });

        $fontSize.on("change", function () {
            $preview.css("fontSize", $fontSize.val() + "px");
            toHtml();
        });

        $wi.on("change", function () {
            if ($wi.val() == "") {
                $preview.css({
                    "width": "auto",
                    "padding-left": "5px",
                    "padding-right": "5px"
                });
            } else {
                $preview.css({
                    "width": $wi.val() + "px",
                    "padding-left": "0",
                    "padding-right": "0"
                });
            }
            toHtml();
        });

        $he.on("change", function () {
            if ($he.val() == "") {
                $preview.css({
                    "height": "auto",
                    "padding-top": "1px",
                    "padding-bottom": "1px",
                    "line-height": ""
                });
            } else {
                $preview.css({
                    "height": $he.val() + "px",
                    "padding-top": "0",
                    "padding-bottom": "0",
                    "line-height": $he.val() + "px"
                });
            }
            toHtml();
        });

        $ta.on("change", function () {
            $preview.css("text-align", $ta.val());
            toHtml();
        });

        // Attach the color picker
        $tc.ColorPicker({
            color: "'. $value .'",
            onChange: function (hsb, hex, rgb) {
                $tc.css("backgroundColor", "#" + hex).val("#" + hex);
                $preview.css("color", "#" + hex).val("#" + hex);
                toHtml();
            }
        });
        $tc.on("change", function () {
            if ($tc.val() == "") {
                $tc.css("backgroundColor", "");
                $preview.css("color", "");
            } else {
                $tc.css("backgroundColor", $tc.val());
                $preview.css("color", $tc.val());
            }
            toHtml();
        });
        $bc.ColorPicker({
            color: "'. $value .'",
            onChange: function (hsb, hex, rgb) {
                $bc.css("backgroundColor", "#" + hex).val("#" + hex);
                $preview.css("backgroundColor", "#" + hex).val("#" + hex);
                toHtml();
            }
        });
        $bc.on("change", function () {
            if ($bc.val() == "") {
                $bc.css("backgroundColor", "");
                $preview.css("backgroundColor", "");
            } else {
                $bc.css("backgroundColor", $bc.val());
                $preview.css("backgroundColor", $bc.val());
            }
            toHtml();
        });
        $borderWidth.on("change", function () {
            if ($borderWidth.val() == "") {
                $preview.css("borderWidth", "");
            } else {
                $preview.css("borderWidth", $borderWidth.val() + "px");
            }
            toHtml();
        });
        $borderStyle.on("change", function () {
            $preview.css("borderStyle", $borderStyle.val());
            toHtml();
        });
        $borderColor.ColorPicker({
            color: "'. $value .'",
            onChange: function (hsb, hex, rgb) {
                $borderColor.css("backgroundColor", "#" + hex).val("#" + hex);
                $preview.css("borderColor", "#" + hex).val("#" + hex);
                toHtml();
            }
        });
        $borderColor.on("change", function () {
            if ($borderColor.val() == "") {
                $borderColor.css("backgroundColor", "");
                $preview.css("borderColor", "");
            } else {
                $borderColor.css("backgroundColor", $borderColor.val());
                $preview.css("borderColor", $borderColor.val());
            }
            toHtml();
        });
        $borderRadius.on("change", function () {
            if ($borderRadius.val() == "") {
                $preview.css("border-radius", "");
            } else {
                $preview.css("border-radius", $borderRadius.val() + "px");
            }
            toHtml();
        });
        $arrow.on("change", function () {
            if ($arrow.val() == 0) {
                $preview.removeClass("arrow");
            } else if ($arrow.val() == 1) {
                $preview.addClass("arrow");
            }
            toHtml();
        });
        $arrowWidth.on("change", function () {
            if ($arrowWidth.val() == "") {
                $preview.children('.label-arrow').css("borderWidth", "");
            } else {
                $preview.children('.label-arrow').css("borderWidth", $arrowWidth.val() + "px");
            }
            toHtml();
        });
        $arrowColor.ColorPicker({
            color: "'. $value .'",
            onChange: function (hsb, hex, rgb) {
                $arrowColor.css("backgroundColor", "#" + hex).val("#" + hex);
                $preview.children('.label-arrow').css("borderColor", "#" + hex).val("#" + hex);
                toHtml();
            }
        });
        $arrowColor.on("change", function () {
            $arrowColor.css("backgroundColor", $arrowColor.val());
            $preview.children('.label-arrow').css("borderColor", $arrowColor.val());
            toHtml();
        });

        function toHtml() {
            $to_html.val($preview.get(0).outerHTML);
        }
    });
});