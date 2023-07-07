<?php


namespace Modo\Gateway\Model;


class Item extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{

    /**#@+
     * Item's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;


    const CACHE_TAG = 'gateway_vendor';

    protected $_cacheTag = 'gateway_vendor';

    protected $_eventPrefix = 'gateway_vendor';

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Modo\Gateway\Model\ResourceModel\Item');
    }


    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }

    /**
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
}