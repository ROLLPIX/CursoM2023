<?php

namespace Rollpix\Elebar\Model;


use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Rollpix\Elebar\Api\NotificationsInterface;
use Rollpix\Elebar\Model\Service\OrderService;
use Rollpix\Elebar\Service\ApiService as RollpixService;
use Rollpix\Elebar\Helper\Data as RollpixHelper;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWKSet;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Jose\Component\Signature\Serializer\JSONFlattenedSerializer;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\Algorithm\RS1;

/**
 * Summary of Notifications
 */
class Notifications implements NotificationsInterface
{
    /**
     * @var RollpixHelper
     */
    private $elebarHelper;

    /**
     * @var bool
     */
    private $debugEnable;

    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * @var SignatureService
     */
    private $signatureService;

    /**
     * @var RollpixService
     */
    private $elebarService;

    /**
     * Summary of __construct
     * @param OrderService $orderService
     * @param RollpixHelper $elebarHelper
     * @param SignatureService $signatureService
     * @param RollpixService $elebarService
     */
    public function __construct(
        OrderService $orderService,
        RollpixHelper $elebarHelper,
        RollpixService $elebarService
    ) {
        $this->orderService = $orderService;
        $this->elebarHelper = $elebarHelper;
        $this->elebarService = $elebarService;
        $this->signatureService = new SignatureService($elebarHelper, $elebarService);
        $this->debugEnable = $this->elebarHelper->isDebugEnabled();
    }

    public function notify($id, $amount, $status, $external_intention_id, $gateway_site_transaction_id, $gateway_transaction_id, $signature)
    {
        $response = ['status' => true, 'message' => 'OK'];
        try {
            $payload = $this->mapPayload($id, $amount, $status, $external_intention_id, $gateway_site_transaction_id, $gateway_transaction_id, $signature);

            $isSignatureValid = $this->signatureService->verifySignature($payload);

            if (!$isSignatureValid) {
                $response['status'] = false;
                $response['message'] = 'The signature of payload notification is not valid.';
            } else {
                $orderResult = $this->orderService->getOrderByIncrementId($external_intention_id);
                if ($orderResult['success']) {
                    /**
                     * @var Order $order
                     */
                    $order = $orderResult['order'];
                    $validatedIntention = $this->orderService->getPaymentIntention($id);
                    if ($validatedIntention['success']) {
                        if ($status == 'ACCEPTED' && $validatedIntention['data']['status'] == 'ACCEPTED') {
                            $order = $this->orderService->approveOrder($order->getId());
                        }
                        if ($status == 'REJECTED' && $validatedIntention['data']['status'] == 'REJECTED') {
                            $order->setStatus($this->elebarHelper->getFailureOrderStatus());
                        }
                        $orderPayment = $order->getPayment();
                        $orderPayment->setAdditionalInformation('elebar_payment_id', $id);
                        $orderPayment->setAdditionalInformation('elebar_status', $status);
                        $this->addOperationCommentToStatusHistory($order, $status, $id);
                        $this->orderService->saveOrder($order);
                    } else {
                        $response['status'] = false;
                        $response['message'] = $validatedIntention['message'];
                    }
                } else {
                    $response['status'] = false;
                    $response['message'] = $orderResult['message'];
                }
            }
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage();
        }

        $request = json_encode(['id' => $id, 'status' => $status, 'external_intention_id' => $external_intention_id]);
        $response = json_encode($response);
        $this->elebarHelper->log("From: \Rollpix\Elebar\Model\Notifications::notify\nREQUEST: $request\nRESPONSE:$response");

        return $response;
    }

    private function addOperationCommentToStatusHistory($order, $status, $id)
    {
        $orderMessage = "Notificación automática de Rollpix: La operación fue %s.<br>";
        $orderMessage .= "Referencia de Pago: %s<br>";
        $orderMessage .= "Estado: %s<br>";
        $operationResult = '';
        switch ($status) {
            case 'SCANNED':
                $operationResult = 'Escaneada';
                break;
            case 'ACCEPTED':
                $operationResult = 'Aceptada';
                break;
            default:
                $operationResult = $status;
                break;
        }
        $order->addCommentToStatusHistory(sprintf($orderMessage, $operationResult, $id, $status));
        return $order;
    }

    private function mapPayload($id, $amount, $status, $external_intention_id, $gateway_site_transaction_id, $gateway_transaction_id, $signature)
    {
        $payloadArray = [
            "id" => $id,
            "external_intention_id" => $external_intention_id,
            "signature" => $signature,
            "status" => $status,
        ];

        if (isset($amount)) {
            $payloadArray["amount"] = $amount;
        }
        if (isset($gateway_site_transaction_id)) {
            $payloadArray["gateway_site_transaction_id"] = $gateway_site_transaction_id;
        }
        if (isset($gateway_transaction_id)) {
            $payloadArray["gateway_transaction_id"] = $gateway_transaction_id;
        }

        return json_encode($payloadArray, true);
    }
}

class SignatureService
{
	/**
     * @var RollpixHelper
     */
    private $elebarHelper;

	/**
     * @var RollpixService
     */
    private $elebarService;

	/**
	 * @var JWKSet
	 */
	private $jwkSet;

	public function __construct
	(
		RollpixHelper $elebarHelper,
		RollpixService $elebarService
	)
	{
        $this->elebarHelper = $elebarHelper;
        $this->elebarService = $elebarService;
		$this->jwkSet = $this->initRollpixKeyStore();
    }

  	/**
     * Summary of initRollpixKeyStore
     * @return mixed
     */
    private function initRollpixKeyStore() {
    	$elebarJwkSet = $this->elebarService->request(
			$this->elebarHelper->getServiceUrl() . '/.well-known/jwks.json',
            null,
            ["User-Agent: 'magento'"],
            "GET"
		);
		return JWKSet::createFromKeyData($elebarJwkSet);
    }

	/**
	 * Summary of verifySignature
	 * @param mixed $signature
	 * @return bool
	 */
	function verifySignature($payload) {
        $body = json_decode($payload);
		
		// Create an AlgorithmManager that supports the HS256 algorithm
		$algorithmManager = new AlgorithmManager([
			new RS256(),
			new RS1(),
		]);

		// Create a new JWSVerifier object with the AlgorithmManager
		$jwsVerifier = new JWSVerifier($algorithmManager);

		// Create a serializer manager and use the JSONFlattenedSerializer
		$serializerManager = new JWSSerializerManager([
			new JSONFlattenedSerializer(),
		]);

        $encodedSignature = json_encode($body->signature);

		// Deserialize the JWS signature into a JWS object
		$jws = $serializerManager->unserialize($encodedSignature);

		// Verify the JWS signature with the JWKSet
		$verificationResult = $jwsVerifier->verifyWithKeySet($jws, $this->jwkSet, 0);

		// Remove the 'signature' property from the payload
		unset($body->signature);

		// Check if the payload of the JWS is equal to the payload
		$payload = json_decode($jws->getPayload(), true);

		$payloadAsArray = json_decode(json_encode($payload), true);
		$bodyAsArray = json_decode(json_encode($body), true);
		
		return $verificationResult && empty(array_diff_assoc($payloadAsArray, $bodyAsArray));
	}
}