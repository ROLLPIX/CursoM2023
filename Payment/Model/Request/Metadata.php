<?php
/**
 * Copyright © Rollpix. All rights reserved.
 * See LICENSE for license details.
 */
namespace Rollpix\Payment\Model\Request;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;

/**
 * Add metadata to checkout request.
 */
class Metadata
{
    /**
     * Build request.
     *
     * @return array
     * phpcs:disable Magento2.Functions.StaticFunction
     */
    public static function build()
    {
        $objectManager = ObjectManager::getInstance();
        /** @var ProductMetadataInterface $productMetadata */
        $productMetadata = $objectManager->get(ProductMetadataInterface::class);
        /** @var ModuleListInterface $moduleList */
        $moduleList = $objectManager->get(ModuleListInterface::class);
        $module = $moduleList->getOne('Rollpix_Payment');

        return [
            'php' => [
                'version' => phpversion()
            ],
            'platform' => [
                'name' => $productMetadata->getName(),
                'edition' => $productMetadata->getEdition(),
                'version' => $productMetadata->getVersion()
            ],
            'module' => [
                'name' => $module['name'],
                'package' => 'rollpix/magento2',
                'version' => $module['setup_version']
            ]
        ];
    }
}
