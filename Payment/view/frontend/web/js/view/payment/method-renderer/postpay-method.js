/**
 * Copyright © Rollpix. All rights reserved.
 * See LICENSE for license details.
 */
define([
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Checkout/js/model/place-order',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'Rollpix_Payment/js/action/set-payment-method',
    'rollpix-js',
    'mage/translate'
], function (
    $,
    Component,
    customerData,
    additionalValidators,
    placeOrderService,
    quote,
    customer,
    setPaymentMethodAction,
    rollpix,
    $t
) {
    'use strict';

    var config = window.checkoutConfig.payment;

    return Component.extend({
        defaults: {
            totals: quote.totals(),
            template: 'Rollpix_Payment/payment/rollpix'
        },

        /**
         * Initialize Rollpix Ui.
         *
         * Is called after knockout renders the payment summary widget.
         */
        initUi: function () {
            rollpix.init(config.rollpix.uiParams);
        },

        /**
         * Get payment method id attribute.
         *
         * @returns {String}
         */
        getId: function () {
            return 'payment-method-' + this.getCode();
        },

        /**
         * Check if payment summary widget is enabled.
         *
         * @returns {Boolean}
         */
        summaryWidgetEnabled: function () {
            return config[this.getCode()].summaryWidget;
        },

        /**
         * Get total amount.
         *
         * @returns {Integer}
         */
        getTotal: function () {
            return Math.round(this.totals.grand_total * 100);
        },

        /**
         * Get currency code.
         *
         * @returns {String}
         */
        getCurrency: function () {
            return this.totals.quote_currency_code;
        },

        /**
         * Get country code.
         *
         * @returns {String}
         */
        getCountry: function () {
            return quote.billingAddress().countryId;
        },

        /**
         * Get number of instalments.
         *
         * @returns {Integer}
         */
        getNumInstalments: function () {
            return config[this.getCode()].numInstalments;
        },

        /**
         * Get payment method icon.
         *
         * @returns {String}
         */
        getIcon: function () {
            return config[this.getCode()].icon;
        },

        /**
         * Get place order button text.
         *
         * @returns {String}
         */
        getButtonText: function () {
            return this.getNumInstalments() === 1 ?
                $t('Pay Now') :
                $t('Continue to Rollpix');
        },

        /**
         * In-context checkout or redirect.
         */
        checkout: function (params) {
            if (config.rollpix.inContext) {
                rollpix.checkout(params.token);
            } else {
                customerData.invalidate(['cart']);
                $.mage.redirect(params.redirect_url);
            }
        },

        /**
         * Place order handler.
         */
        continueToRollpix: function () {
            if (additionalValidators.validate()) {
                this.selectPaymentMethod();
                var self = this;
                var payload = {};

                if (!customer.isLoggedIn()) {
                    payload['email'] = quote.guestEmail;
                }

                setPaymentMethodAction(this.messageContainer).done(function () {
                    placeOrderService(config.rollpix.checkoutUrl, payload, self.messageContainer)
                        .done(function (response) {
                            self.checkout(response);
                        })
                        .fail(function (response) {
                            self.messageContainer.addErrorMessage(response.message);
                        });
                    }
                );
            }
        }
    });
});
