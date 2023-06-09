/**
 * Copyright © Rollpix. All rights reserved.
 * See LICENSE for license details.
 */
define([
    'jquery',
    'rollpix-js'
], function ($, rollpix) {
    'use strict';

    var config = window.checkoutConfig;

    $.widget('mage.rollpixUi', {
        options: config && config.payment.rollpix.uiParams,

        /** @inheritdoc */
        _create: function () {
            rollpix.init(this.options);
        }
    });

    return $.mage.rollpixUi;
});
