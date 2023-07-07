require(
    [
        'jquery',
        'mage/url'
    ],
    function(
        $,
        url
    ) {
        const orderid = new URL(window.location.href).searchParams.get("orderid");
        const operationUrl = window.location.origin + '/modo/order/operations';
        const redirectOperationUrl = operationUrl + '/operation/redirect_to_onepage/orderid/'+ orderid + '/onepage/';
        const cancelOrderUrl = operationUrl + '/operation/cancel_order/orderid/' + orderid;

        function createPaymentIntention(){
            var paymentIntentionData = null;
            $.ajax({
                url: operationUrl,
                type: 'POST',
                dataType: 'json',
                showLoader: true,
                async: false,
                data: {
                    operation: 'generate_payment_intention',
                    orderid: orderid,
                },
                complete: function (response){
                    paymentIntentionData = response.responseJSON.data;
                },
            });
            return paymentIntentionData;
        }

        function openModal() {
            let paymentIntention = createPaymentIntention();
            if(paymentIntention === ''){
                alert('Excedió el tiempo limite para efectuar el pago. Su orden fue cancelada.')
            }
            else {
                var options = {
                    qrString: paymentIntention.qr,
                    checkoutId: paymentIntention.id,
                    deeplink: {
                        url: paymentIntention.deeplink,
                        callbackURL: redirectOperationUrl + 'failure',
                        callbackURLSuccess: redirectOperationUrl + 'success',
                    },
                    onCancel: function () {
                        window.location.replace(cancelOrderUrl);
                    },
                    refreshData: function () {
                        var paymentIntention = createPaymentIntention();
                        var options = {
                            qrString: paymentIntention.qr,
                            checkoutId: paymentIntention.id,
                            deeplink: {
                                url: paymentIntention.deeplink,
                                callbackURL: redirectOperationUrl + 'failure',
                                callbackURLSuccess: redirectOperationUrl + 'success',
                            }
                        }
                        return options;
                    },
                    callbackURL: redirectOperationUrl + 'success'
                }
                ModoSDK.modoInitPayment(options)
            }
        }

        function sendRequest(operation, onComplete, onError) {
            if(onError == null){
                onError = function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                }
            }

        }


        $("#pay-with-modo").on('click',function(){
            openModal();
        });
        openModal();
    }
);