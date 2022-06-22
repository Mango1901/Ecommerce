<?php

namespace Magenest\Slider\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Magenest\Slider\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('magenest_slider_entity')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magenest_slider_entity')
            )
                ->addColumn(
                    'slider_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'Slider ID'
                )
                ->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Name'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    2,
                    [
                        'nullable' => false,
                        'default' => 1
                    ],
                    'disable or enable slider'
                )
                ->addColumn(
                    'type',
                    Table::TYPE_TEXT,
                    '255',
                    ['nullable => true'],
                    'banner, single slider, 2 sliders'
                )
                ->addColumn(
                    'data_source',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true
                    ],
                    'Data form'
                )->addColumn(
                    'position',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true
                    ],
                    'position display'
                )
                ->addColumn(
                    'parent_id',
                    Table::TYPE_INTEGER,
                    11,
                    ['nullable => false','default' => 0],
                    'Main slider id'
                )
                ->setComment('Slider Table');
            $installer->getConnection()->createTable($table);
        }
        if (!$installer->tableExists('magenest_slider_item_entity')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magenest_slider_item_entity')
            )
                ->addColumn(
                    'item_id',
                    Table::TYPE_INTEGER,
                    10,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'Item ID'
                )
                ->addColumn(
                    'slider_id',
                    Table::TYPE_INTEGER,
                    11,
                    [
                        'nullable => false'
                    ],
                    'Slider ID'
                )
                ->addColumn(
                    'data_source',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Data in form'
                )
                ->addColumn(
                    'order_number',
                    Table::TYPE_INTEGER,
                    10,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Item ID'
                )
                ->addIndex(
                    $installer->getIdxName(
                        'magenest_slider_item_entity',
                        ['slider_id', 'order_number'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['slider_id', 'order_number'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->setComment('Item Table');
            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists('magenest_slider_preview')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magenest_slider_preview')
            )
                ->addColumn(
                    'preview_id',
                    Table::TYPE_INTEGER,
                    10,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'Item ID'
                )
                ->addColumn(
                    'key_id',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false
                    ],
                    'key md5 encrypt'
                )
                ->addColumn(
                    'slider_id',
                    Table::TYPE_INTEGER,
                    11,
                    [
                        'nullable' => true
                    ],
                    'Slider ID'
                )
                ->addColumn(
                    'config',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true
                    ],
                    'Config'
                )
                ->addColumn(
                    'slider_data',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true
                    ],
                    'Slider data'
                )
                ->addColumn(
                    'childSlider',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'child slider data'
                )
                ->setComment('Preview data');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
