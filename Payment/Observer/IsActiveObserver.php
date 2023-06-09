<?php
/**
 * Copyright © Rollpix. All rights reserved.
 * See LICENSE for license details.
 */
namespace Rollpix\Payment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Rollpix\Payment\Gateway\Config\Config;
use Rollpix\Payment\Model\Method\AbstractRollpixMethod;

/**
 * Check payment method availability.
 */
class IsActiveObserver implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        $methodInstance = $event->getMethodInstance();

        if ($methodInstance instanceof AbstractRollpixMethod && !$this->config->isAvailable()) {
            /** @var \Magento\Framework\DataObject $result */
            $result = $observer->getEvent()->getResult();
            $result->setData('is_available', false);
        }
    }
}
