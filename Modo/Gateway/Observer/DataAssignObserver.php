<?php

namespace Modo\Gateway\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;

class DataAssignObserver extends AbstractDataAssignObserver
{
    const MODO_PAYMENT_ID = 'modo_payment_id';
    const MODO_STATUS = 'modo_status';
    const MODO_ORDER_KEY = 'modo_order_key';
    const MODO_QUOTE_ID = 'modo_quote_id';

    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::MODO_PAYMENT_ID,
        self::MODO_STATUS,
        self::MODO_ORDER_KEY,
    ];

    /**
     * @var MaskedQuoteIdToQuoteIdInterface
     */
    private $maskedQuoteIdToQuoteId;

    public function __construct(
        MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
    )
    {
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (is_array($additionalData)) {
            $paymentInfo = $this->readPaymentModelArgument($observer);

            foreach ($this->additionalInformationList as $additionalInformationKey) {
                if (isset($additionalData[$additionalInformationKey])) {
                    if ($additionalInformationKey == self::MODO_ORDER_KEY && isset($additionalData[self::MODO_QUOTE_ID])) {
                        if(!is_numeric($additionalData[self::MODO_QUOTE_ID])){
                            $additionalData[self::MODO_QUOTE_ID] = $this->maskedQuoteIdToQuoteId->execute($additionalData[self::MODO_QUOTE_ID]);
                        }
                        $additionalData[$additionalInformationKey] = base64_encode($additionalData[$additionalInformationKey] . ':' . $additionalData[self::MODO_QUOTE_ID]);
                    }
                    $paymentInfo->setAdditionalInformation($additionalInformationKey, $additionalData[$additionalInformationKey]);
                }
            }
        }
    }
}
