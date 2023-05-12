<?php

namespace Rollpix\Shipping\Model;

use Magento\Framework\Model\AbstractModel;
use Rollpix\Shipping\Model\ResourceModel\ShippingMethod as ShippingMethodRM;

class ShippingMethod extends AbstractModel{
    protected function _construct() {
        $this->_init( ShippingMethodRM::class );
    }

    public function getAvailableStatuses() : array {
        return [
            '1' => 'Enable',
            '0' => 'Disable',
        ];
    }
}
