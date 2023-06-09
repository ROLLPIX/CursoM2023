/**
 * Copyright © Rollpix. All rights reserved.
 * See LICENSE for license details.
 */
define([
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/set-payment-information'
], function (quote, setPaymentInformation) {
    'use strict';

    return function (messageContainer) {
        return setPaymentInformation(messageContainer, quote.paymentMethod());
    };
});
