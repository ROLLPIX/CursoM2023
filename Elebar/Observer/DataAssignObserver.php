<?php

namespace Rollpix\Elebar\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;

class DataAssignObserver extends AbstractDataAssignObserver
{
    const Elebar_PAYMENT_ID = 'elebar_payment_id';
    const Elebar_STATUS = 'elebar_status';
    const Elebar_ORDER_KEY = 'elebar_order_key';
    const Elebar_QUOTE_ID = 'elebar_quote_id';

    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::Elebar_PAYMENT_ID,
        self::Elebar_STATUS,
        self::Elebar_ORDER_KEY,
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
                    if ($additionalInformationKey == self::Elebar_ORDER_KEY && isset($additionalData[self::Elebar_QUOTE_ID])) {
                        if(!is_numeric($additionalData[self::Elebar_QUOTE_ID])){
                            $additionalData[self::Elebar_QUOTE_ID] = $this->maskedQuoteIdToQuoteId->execute($additionalData[self::Elebar_QUOTE_ID]);
                        }
                        $additionalData[$additionalInformationKey] = base64_encode($additionalData[$additionalInformationKey] . ':' . $additionalData[self::Elebar_QUOTE_ID]);
                    }
                    $paymentInfo->setAdditionalInformation($additionalInformationKey, $additionalData[$additionalInformationKey]);
                }
            }
        }
    }
}
