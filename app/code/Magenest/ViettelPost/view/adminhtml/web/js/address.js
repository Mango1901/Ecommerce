/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'uiComponent',
        'underscore',
        'ko'
    ],
    function ($, Component, _, ko) {
        'use strict';

        return Component.extend({
            defaults: {
                template: "Magenest_ViettelPost/address",
                displayViettelPostField: ko.observable(false),
                provinceId: ko.observable(0),
                districtId: ko.observable(0),
                wardsId: ko.observable(0),
                provinceArr: "",
                districtArr: "",
                wardsArr: "",
                districtArrKo: ko.observableArray([]),
                wardsArrKo: ko.observableArray([]),
            },

            /**
             * Initialize observable properties
             */
            initObservable: function () {
                var self = this;
                this._super();
                $('#carrier_select').on("change", function () {
                    self.checkShouldDisplayNote();
                });
                this.provinceArr = JSON.parse(this.province);
                this.districtArr = JSON.parse(this.district);
                this.wardsArr = JSON.parse(this.wards);
                this.checkShouldDisplayNote();
                return this;
            },

            changeDistrictArr: function(){
                var self = this;
                this.districtArr.each(function (item, i) {
                    if (item.province_id == self.provinceId()) {
                        var temp = [];
                        temp.district_id = item.district_id;
                        temp.district_name = item.district_name;
                        self.districtArrKo.push(temp);
                    }
                });
                this.emptySelectOption('#ward_id');
            },

            changeWardsArr: function(){
                var self = this;
                this.wardsArr.each(function (item, i) {
                    if (item.district_id == self.districtId()) {
                        var temp = [];
                        temp.wards_id = item.wards_id;
                        temp.wards_name = item.wards_name;
                        self.wardsArrKo.push(temp);
                    }
                });
            },

            emptySelectOption: function(id){
                $(id).empty();
                $(id).append("<option value=''>Please select</option>");
            },

            fillSelectOption: function(id, data){
                //sort by name
                var sortedArr = data.slice(0);
                sortedArr.sort(function(a,b) {
                    var x = a.label.toLowerCase();
                    var y = b.label.toLowerCase();
                    return x < y ? -1 : x > y ? 1 : 0;
                });
                this.emptySelectOption(id);
                sortedArr.each(function (item, i) {
                    $(id).append("<option value=\""+item.value+"\">"+item.label+"</option>")
                });
            },

            checkShouldDisplayNote:  function () {
                this.displayViettelPostField($('#carrier_select').val()=='viettel_post');
            }

        });
    }
);
