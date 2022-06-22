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
    function($, ko) {

        return function (pageData) {
            return {
                title :ko.observable(pageData.title),
                page_id  : ko.observable(pageData.id),
                link  :ko.observable(pageData.link),

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