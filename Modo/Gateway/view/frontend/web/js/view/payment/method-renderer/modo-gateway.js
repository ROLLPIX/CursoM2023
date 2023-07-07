
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
                template: 'Modo_Gateway/payment/form',
                code: 'modo_gateway',
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
                        'modo_order_key': checkoutConfig.formKey,
                        'modo_quote_id': quote.getQuoteId(),
                        'modo_payment_id': '',
                        'modo_status': ''
                    }
                };
            },



            afterPlaceOrder: function () {
                var quoteid = quote.getQuoteId();
                $.ajax({
                    url: window.location.origin + '/modo/order/operations',//url.build(window.checkoutConfig.payment[this.getCode()].operations),
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
                return window.checkoutConfig.payment.modo_gateway.banner;
            },

            getDesktopBannerUrl: function(){
                return window.checkoutConfig.payment.modo_gateway.desktop_banner;
            },

            getMobileBannerUrl: function(){
                return window.checkoutConfig.payment.modo_gateway.mobile_banner;
            },

            getVerticalBannerUrl: function (){
                return window.checkoutConfig.payment.modo_gateway.vertical_banner;
            },

            getHorizontalBannerUrl: function (){
                return window.checkoutConfig.payment.modo_gateway.horizontal_banner;
            }
        })
    }
);