define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_SalesRule/js/action/set-coupon-code',
        'Magento_SalesRule/js/view/payment/discount',
        'mage/translate',
        'Magento_Catalog/js/price-utils',
        'Magento_SalesRule/js/model/coupon',
        'mage/url'
    ],
    function ($, ko, Component, setCouponCodeAction, discount, $t, priceUtils, coupon, urlBuilder) {
        "use strict";
        var fields = {
            'is_active': $t('is not active'),
            'from_date': $t('is not started yet'),
            'to_date': $t('is out of date'),
            'customer_group_id': $t('belongs to another group'),
            'website_id': $t('belongs to another website'),
            'usage_limit': $t('is usage limit exceeded'),
            'usage_per_customer': $t('is usage limit exceeded per customer')
        };
        var currentPage = '';
        return Component.extend({
            defaults: {
                template: 'Magenest_CouponCode/codeList'
            },

            customer_coupon: ko.observableArray(),

            rules: ko.observableArray(),

            reason: ko.observable(),

            messageError: ko.observable(""),

            isApplied: ko.observable(false),

            enable: window.checkoutConfig.enable,

            initialize: function () {
                this._super();
                if (window.checkoutConfig.customerData.id != null) {
                    this.rules = ko.observableArray(window.checkoutConfig.customer_coupon);
                } else {
                    this.rules = ko.observableArray(window.checkoutConfig.rules);
                }
                this.isApplied = window.checkoutConfig.is_applied;
                currentPage = this.current_page;
                return this;
            },

            showExpiring: function (rule) {
                var result = '';
                if (rule.to_date !== null && !this.validateCondition(rule, 'to_date')) {
                    var toDate = new Date(rule.to_date);
                    result = $t('Valid until: ') + toDate.toDateString();
                }
                return result;
            },

            getTitleHtmlOfCoupon: function (rule) {
                let discountAmount = null;
                if (rule['simple_action'] === 'by_percent') {
                    discountAmount = Math.round(rule['discount_amount']);
                } else {
                    discountAmount = priceUtils.formatPrice(rule['discount_amount']);
                }

                return $t("Input Coupon <span class='discount-code'>%1</span> to get discount <span class='discount-value'>%2</span> for %3")
                    .replace('%1', rule['code'])
                    .replace('%2', discountAmount)
                    .replace('%3', rule['name']);
            },

            dateStyling: function (rule) {
                var result = '';
                var nowData = new Date(this.getNowDate());
                if (rule.to_date !== '') {
                    var expiring = new Date(rule.to_date);
                    var differenceInTime = expiring.getTime() - nowData.getTime();
                    var differenceInDays = differenceInTime / (1000 * 3600 * 24);
                    if (differenceInDays + 1 <= 7) {
                        result = 'expiring';
                    }
                }
                return result;
            },

            showDescription: function (rule) {
                var result = "";

                if (rule.description !== '') {
                    result = rule.code + ' - ' + rule.description;
                } else result = rule.code;

                return $t(result);
            },

            showUsesPerCoupon: function (rule) {
                if (rule.usage_limit) {
                    var usage = rule.usage_limit - rule.times_used;
                    return $t('Limited Offer: ') + usage.toString();
                }
            },

            showUsesPerCustomer: function (rule) {
                if (rule.usage_per_customer) {
                    if (!rule.times_used) {
                        rule.times_used = 0;
                    }
                    var usage = rule.usage_per_customer - rule.times_used_per_customer;
                    return $t('Awarded: ') + usage.toString();
                }
            },

            useNow: function () {
                let value = $("[name='coupon']:checked").val();
                if (value) {
                    this.messageError('');
                    $('#coupon_code').val(value);
                    if (currentPage !== "checkout_cart_index") {
                        coupon.setCouponCode(value);
                    }

                    $("#apply_coupon").trigger("click");
                    $("#modal").modal("closeModal");
                } else {
                    this.messageError($t('Please choose coupon to apply'));
                }
            },

            closePopup: function () {
                $("#modal").modal("closeModal");
            },

            checkConfigurationValidate: function (rule) {
                var self = this;
                var str = '';
                var flag = true;
                var conditionFields = [
                    'is_active',
                    'from_date',
                    'to_date',
                    'customer_group_id',
                    'website_id',
                    'usage_limit',
                    'usage_per_customer',
                ];
                for (var i = 0; i < conditionFields.length; i++) {
                    if (self.validateCondition(rule, conditionFields[i])) {
                        flag = false;
                        str += this.checkField(fields[conditionFields[i]]);
                    }
                }
                var result = $t('The coupon code ') + str.substring(0, str.length - 2).concat('.');
                this.reason = ko.observable(result);

                return flag;
            },

            checkField: function (value) {
                value += ', ';
                return value;
            },

            getConditionUrl: function (data) {
                return urlBuilder.build(`voucher/details?evcode=${data.code}&ruleId=${data.rule_id}`);
            },

            validateCondition: function (rule, field) {
                if (typeof rule[field] === undefined || rule[field] == null) {
                    return false;
                }
                var result = false;
                var customer_group_id = window.checkoutConfig.customerData['group_id'];
                var customer_id = window.checkoutConfig.customerData['id'];
                var website_id = window.checkoutConfig.website_id;
                if (customer_id == null) {
                    customer_group_id = '0';
                }
                var nowDate = this.getNowDate();

                switch (field) {
                    case 'is_active':
                        result = rule.is_active === '0';
                        break;
                    case 'usage_per_customer':
                        result = customer_id != null && (parseInt(rule.usage_per_customer) - parseInt(rule.times_used_per_customer)) === 0;
                        break;
                    case 'usage_limit':
                        result = (parseInt(rule.usage_limit) - parseInt(rule.times_used)) === 0;
                        break;
                    case 'website_id':
                        result = rule.website_id.indexOf(website_id) < 0;
                        break;
                    case 'customer_group_id':
                        result = rule.customer_group_id.indexOf(customer_group_id) < 0;
                        break;
                    case 'from_date':
                        result = rule.from_date > nowDate;
                        break;
                    case 'to_date':
                        result = rule.to_date < nowDate;
                        break;
                    default:
                        break;
                }

                return result;
            },

            getNowDate: function () {
                var d = new Date();
                var month = d.getMonth() + 1;
                var day = d.getDate();
                var nowDate = d.getFullYear() + '-' +
                    (month < 10 ? '0' : '') + month + '-' +
                    (day < 10 ? '0' : '') + day;
                return nowDate;
            },

            showImages: function (rule) {
                if (rule.images) {
                    var data = JSON.parse(rule.images)
                    var url = data[0].url
                    return url;
                } else {
                    return window.checkoutConfig.default_image;
                }
            }
        });

    }
);
