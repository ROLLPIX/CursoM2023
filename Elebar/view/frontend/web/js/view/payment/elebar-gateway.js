
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        let elebarGatewayType = 'elebar_gateway';
        if(window.checkoutConfig.payment[elebarGatewayType].active) {
            rendererList.push(
                {
                    type: elebarGatewayType,
                    component: 'Rollpix_Elebar/js/view/payment/method-renderer/elebar-gateway'
                }
            );
        }

        return Component.extend({});
    }
);
