<?php

namespace Rollpix\Shipping\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb ;

class ShippingMethod extends AbstractDb {
    private const MAIN_TABLE    = 'rollpix_shipping_methods' ;
    private const ID_FIELD_NAME = 'method_id' ;

    protected function _construct() {
        $this->_init( self::MAIN_TABLE, self::ID_FIELD_NAME );
    }
}
