<?php

namespace Rollpix\Elebar\Test\Unit\Observer;


use Magento\Framework\DataObject;
use Magento\Framework\Event;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Rollpix\Elebar\Observer\DataAssignObserver;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;

class DataAssignObserverTest extends \PHPUnit\Framework\TestCase
{
    const PAYMENT_METHOD_NONCE = 'nonce';
    const DEVICE_DATA = '{"test": "test"}';

    public function testExecute()
    {
        $observerContainer = $this->getMockBuilder(Event\Observer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event = $this->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->getMock();
        $maskedQuoteIdToQuoteId = $this->getMockBuilder(MaskedQuoteIdToQuoteIdInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $paymentInfoModel = $this->createMock(InfoInterface::class);
        $dataObject = new DataObject(
            [
                PaymentInterface::KEY_ADDITIONAL_DATA => [
                    //'payment_method_nonce' => self::PAYMENT_METHOD_NONCE,
                    //'device_data' => self::DEVICE_DATA
                    \Rollpix\Elebar\Observer\DataAssignObserver::Elebar_PAYMENT_ID => '',
                    \Rollpix\Elebar\Observer\DataAssignObserver::Elebar_STATUS => '',
                    \Rollpix\Elebar\Observer\DataAssignObserver::Elebar_ORDER_KEY => '',
                    \Rollpix\Elebar\Observer\DataAssignObserver::Elebar_QUOTE_ID => ''
                ]
            ]
        );
        $observerContainer->expects(static::atLeastOnce())
            ->method('getEvent')
            ->willReturn($event);
        $event->expects(static::exactly(2))
            ->method('getDataByKey')
            ->willReturnMap(
                [
                    [AbstractDataAssignObserver::MODEL_CODE, $paymentInfoModel],
                    [AbstractDataAssignObserver::DATA_CODE, $dataObject]
                ]
            );
        $paymentInfoModel->expects(static::at(0))
            ->method('setAdditionalInformation')
            ->with(\Rollpix\Elebar\Observer\DataAssignObserver::Elebar_PAYMENT_ID, '');
        $paymentInfoModel->expects(static::at(1))
            ->method('setAdditionalInformation')
            ->with(\Rollpix\Elebar\Observer\DataAssignObserver::Elebar_STATUS, '');
        $paymentInfoModel->expects(static::at(2))
            ->method('setAdditionalInformation')
            ->with(\Rollpix\Elebar\Observer\DataAssignObserver::Elebar_ORDER_KEY, 'OjA=');

        $observer = new DataAssignObserver($maskedQuoteIdToQuoteId);
        $observer->execute($observerContainer);
    }
}
