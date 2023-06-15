<?php
/**
 * Copyright © Rollpix. All rights reserved.
 * See LICENSE for license details.
 */
namespace Rollpix\Payment\Model\Method;

use Rollpix\Payment\Gateway\Config\PayNowConfig;

/**
 * Rollpix pay now method.
 */
class PayNow extends AbstractPaymentMethod
{
    /**
     * Number of instalments
     */
    const NUM_INSTALMENTS = 1;

    /**
     * @var string
     */
    protected $_code = PayNowConfig::CODE;
}
