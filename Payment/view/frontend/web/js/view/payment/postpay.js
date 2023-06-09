/**
 * Copyright © Rollpix. All rights reserved.
 * See LICENSE for license details.
 */
define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    'use strict';

    rendererList.push(
        {
            type: 'rollpix',
            component: 'Rollpix_Payment/js/view/payment/method-renderer/rollpix-method'
        },
        {
            type: 'rollpix_pay_now',
            component: 'Rollpix_Payment/js/view/payment/method-renderer/rollpix-method'
        }
    );
    return Component.extend({});
});
