<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Rollpix\Shipping\Model\ResourceModel\ShippingMethod;

use \Rollpix\Shipping\Model\ResourceModel\AbstractCollection;
use \Rollpix\Shipping\Model\ResourceModel\ShippingMethod as ShippingMethodRM;
use \Rollpix\Shipping\Model\ShippingMethod ;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'method_id';

    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init( ShippingMethod::class, ShippingMethodRM::class );
        // $this->_map['fields']['method_id'] = 'main_table.method_id';
    }
}
