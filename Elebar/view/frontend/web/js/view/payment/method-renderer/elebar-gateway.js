
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'mage/url',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, Component, url, quote) {
        'use strict';

        return Component.extend({
            defaults: {
                redirectAfterPlaceOrder: false,
                template: 'Rollpix_Elebar/payment/form',
                code: 'elebar_gateway',
                active: false,
                transactionResult: ''
            },

            getCode: function() {
                return this.code;
            },

            getTitle: function () {
                return window.checkoutConfig.payment[this.getCode()].title;
            },

            getData: function() {
                return {
                    'method': this.getCode(),
                    'additional_data': {
                        'elebar_order_key': checkoutConfig.formKey,
                        'elebar_quote_id': quote.getQuoteId(),
                        'elebar_payment_id': '',
                        'elebar_status': ''
                    }
                };
            },



            afterPlaceOrder: function () {
                var quoteid = quote.getQuoteId();
                $.ajax({
                    url: window.location.origin + '/elebar/order/operations',//url.build(window.checkoutConfig.payment[this.getCode()].operations),
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        operation: 'purchase',
                        quoteid: quoteid,
                        order_key: checkoutConfig.formKey
                    },
                    complete: function (response) {
                        window.location.replace(url.build(response.responseJSON.url));
                    },
                    error: function (xhr, status, errorThrown) {
                        console.log('Error happens. Try again.');
                    }
                });
            },

            getBannerUrl: function(){
                return window.checkoutConfig.payment.elebar_gateway.banner;
            },

            getDesktopBannerUrl: function(){
                return window.checkoutConfig.payment.elebar_gateway.desktop_banner;
            },

            getMobileBannerUrl: function(){
                return window.checkoutConfig.payment.elebar_gateway.mobile_banner;
            },

            getVerticalBannerUrl: function (){
                return window.checkoutConfig.payment.elebar_gateway.vertical_banner;
            },

            getHorizontalBannerUrl: function (){
                return window.checkoutConfig.payment.elebar_gateway.horizontal_banner;
            }
        })
    }
);