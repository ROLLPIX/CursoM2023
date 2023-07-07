
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
        let modoGatewayType = 'modo_gateway';
        if(window.checkoutConfig.payment[modoGatewayType].active) {
            rendererList.push(
                {
                    type: modoGatewayType,
                    component: 'Modo_Gateway/js/view/payment/method-renderer/modo-gateway'
                }
            );
        }

        return Component.extend({});
    }
);
