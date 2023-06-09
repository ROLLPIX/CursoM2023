<?php
/**
 * Copyright © Rollpix. All rights reserved.
 * See LICENSE for license details.
 */
namespace Rollpix\Payment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Rollpix\Payment\Model\Method\AbstractRollpixMethod;

/**
 * Disable payment method on admin site.
 */
class IsActiveAdminObserver implements ObserverInterface
{
    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        $methodInstance = $event->getMethodInstance();

        if ($methodInstance instanceof AbstractRollpixMethod) {
            /** @var \Magento\Framework\DataObject $result */
            $result = $observer->getEvent()->getResult();
            $result->setData('is_available', false);
        }
    }
}
