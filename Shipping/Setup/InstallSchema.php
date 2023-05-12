<?php

namespace Rollpix\Shipping\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'banners'
         */
        $table = $installer->getConnection()->newTable($installer->getTable('rollpix_shipping_methods'))
                           ->addColumn(
                'method_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
                [ 'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true ],
                'Method Id'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null,
                ['nullable' => false, 'unsigned' => true, 'default' => '1'],
                'Enable'
            )->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 30,
                [ 'nullable' => false ],
                'Method Title'
            )->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255,
                [ 'nullable' => false, 'unique' => true ],
                'Method code'
            )->addColumn(
                'cost',
                \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT, null,
                [ 'nullable' => false ],
                'Method cost'
            )->addColumn(
                'sort_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
                ['nullable' => false, 'unsigned' => true, 'default' => '0'],
                'Sort Order'
            )->addColumn(
                'created_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Creation Time'
            )->addColumn(
                'update_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Update Time'
            );


        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
