<?php
namespace Rollpix\Elebar\Model\Item\Source;

class IsActive implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Rollpix\Item\Model\Item
     */
    protected $item;

    /**
     * Constructor
     *
     * @param \Rollpix\Item\Model\Item $item
     */
    public function __construct(\Rollpix\Elebar\Model\Item $item)
    {
        $this->item = $item;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->item->getAvailableStatuses();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
