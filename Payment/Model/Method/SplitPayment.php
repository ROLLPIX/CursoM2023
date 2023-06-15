<?php
/**
 * Copyright © Rollpix. All rights reserved.
 * See LICENSE for license details.
 */
namespace Rollpix\Payment\Model\Method;

use Rollpix\Payment\Gateway\Config\Config;

/**
 * Rollpix split payment method.
 */
class SplitPayment extends AbstractPaymentMethod
{
    /**
     * @var string
     */
    protected $_code = Config::CODE;
}
