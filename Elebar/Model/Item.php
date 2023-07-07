<?php


namespace Rollpix\Elebar\Model;


class Item extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{

    /**#@+
     * Item's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;


    const CACHE_TAG = 'elebar_vendor';

    protected $_cacheTag = 'elebar_vendor';

    protected $_eventPrefix = 'elebar_vendor';

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Rollpix\Elebar\Model\ResourceModel\Item');
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