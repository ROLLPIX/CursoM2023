<?php

namespace Rollpix\Elebar\Model\ResourceModel;


class Item extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('elebar_vendors', 'item_id');
    }
}