<?php
/**
 * Copyright © Rollpix. All rights reserved.
 * See LICENSE for license details.
 */
namespace Rollpix\Payment\Model\Config\Source;

/**
 * Source model for Rollpix theme
 */
class Theme implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'light', 'label' => __('light')],
            ['value' => 'dark', 'label' => __('dark')]
        ];
    }
}
