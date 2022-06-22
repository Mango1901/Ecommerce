/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'ko'
    ],
    function($, ko, refreshPageModelAction) {
        return function (catData) {
            return {
                title :ko.observable(catData.title),
                cat_id  : ko.observable(catData.entity_id),
                link  :ko.observable(catData.link),
                visible: ko.observable(true),


                setImageSource: function (title) {
                    this.title(title);
                },
                getTitle: function () {
                    return this.title;
                },
                getLink: function () {
                    return this.link;
                },
                setLink : function (link) {
                    this.link (link);
                }
            };
        }
    }
);