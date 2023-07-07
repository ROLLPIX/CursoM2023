<?php
namespace Modo\Gateway\Model\Item\Source;

class IsActive implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Modo\Item\Model\Item
     */
    protected $item;

    /**
     * Constructor
     *
     * @param \Modo\Item\Model\Item $item
     */
    public function __construct(\Modo\Gateway\Model\Item $item)
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
