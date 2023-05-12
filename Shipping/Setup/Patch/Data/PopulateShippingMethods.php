<?php declare( strict_types = 1 ) ;

namespace Rollpix\Shipping\Setup\Patch\Data ;

use Magento\Framework\Setup\ModuleDataSetupInterface ;
use Magento\Framework\Setup\Patch\DataPatchInterface ;
use Rollpix\Shipping\Model\ResourceModel\ShippingMethod as ShippingMethodRM ;
use Rollpix\Shipping\Model\ShippingMethodFactory ;

class PopulateShippingMethods implements DataPatchInterface {
    private ModuleDataSetupInterface $moduleDataSetup ;
    private ShippingMethodFactory    $shippingMethodFactory ;
    private ShippingMethodRM         $shippingMethodRM ;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ShippingMethodFactory    $shippingMethodFactory,
        ShippingMethodRM         $shippingMethodRM
    ) {
        $this->moduleDataSetup       = $moduleDataSetup ;
        $this->shippingMethodFactory = $shippingMethodFactory ;
        $this->shippingMethodRM      = $shippingMethodRM ;
    }

    public static function getDependencies() : array {
        return [] ;
    }

    public function getAliases() : array {
        return [] ;
    }

    public function apply() : void {
        $this->moduleDataSetup->startSetup() ;

        $method = $this->shippingMethodFactory->create() ;
        $method->setData( [
            ''
        ] ) ;
        $this->shippingMethodRM->save( $method ) ;

        $this->moduleDataSetup->endSetup() ;
    }
}