<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/10/16
 * Time: 09:58
 */

namespace Magenest\MapList\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magenest\MapList\Helper\Constant;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $tableName = $installer->getTable(Constant::LOCATION_TABLE);
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'location_id',
                    Table::TYPE_INTEGER,
                    null,
                    array(
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ),
                    'Location table primary key'
                )
                ->addColumn('title', Table::TYPE_TEXT, 255, array('nullable' => false), 'Title')
                ->addColumn('description', Table::TYPE_TEXT, '2M', array(), 'Description')
                ->addColumn(
                    'latitude', Table::TYPE_DECIMAL, null, array(
                    'precision' => 10,
                    'scale' => 8,
                    'nullable' => false
                    ), 'Latitude'
                )
                ->addColumn(
                    'longitude', Table::TYPE_DECIMAL, null, array(
                    'precision' => 11,
                    'scale' => 8,
                    'nullable' => false
                    ), 'Longitude'
                )
                ->addColumn('short_description', Table::TYPE_TEXT, 1000, array(), 'Short Description')
                ->addColumn('address', Table::TYPE_TEXT, 1000, array(), 'Address')
                ->addColumn('website', Table::TYPE_TEXT, 2000, array(), 'Website')
                ->addColumn('email', Table::TYPE_TEXT, 255, array(), 'Email')
                ->addColumn('phone_number', Table::TYPE_TEXT, 20, array(), 'Phone Number')
                ->addColumn('big_image', Table::TYPE_TEXT, null, array(), 'Big image')
                ->addColumn('small_image', Table::TYPE_TEXT, null, array(), 'Small image')
                ->addColumn(
                    'is_active',
                    Table::TYPE_TEXT,
                    10,
                    array('nullable' => false, 'default' => '1'),
                    'Is Post Active?'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    array('nullable' => false, 'default' => Table::TIMESTAMP_INIT),
                    'Creation Time'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    array('nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE),
                    'Update Time'
                )
                ->setComment('Magenest Location Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $tableName = $installer->getTable(Constant::CATEGORY_TABLE);
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            $table = $installer->getConnection()->newTable($tableName)
                ->addColumn(
                    'category_id',
                    Table::TYPE_INTEGER,
                    null,
                    array(
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ),
                    'Category table primary key'
                )
                ->addColumn('title', Table::TYPE_TEXT, 512, array('nullable' => false), 'Title')
                ->addColumn('description', Table::TYPE_TEXT, '2M', array(), 'Description')
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    array('nullable' => false, 'default' => Table::TIMESTAMP_INIT),
                    'Creation Time'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    array('nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE),
                    'Update Time'
                )
                ->setComment('Magenest Category table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $tableName = $installer->getTable(Constant::LOCATION_CATEGORY_TABLE);
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            $table = $installer->getConnection()->newTable($tableName)
                ->addColumn(
                    'location_category_id',
                    Table::TYPE_INTEGER,
                    null,
                    array(
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ),
                    'Location Category table primary key'
                )
                ->addColumn(
                    Constant::LOCATION_TABLE_ID,
                    Table::TYPE_INTEGER,
                    null,
                    array('unsigned' => true),
                    'Reference From Location table'
                )
                ->addColumn(
                    Constant::CATEGORY_TABLE_ID,
                    Table::TYPE_INTEGER,
                    null,
                    array('unsigned' => true),
                    'Reference From CAtegory table'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    array('nullable' => false, 'default' => Table::TIMESTAMP_INIT),
                    'Creation Time'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    array('nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE),
                    'Update Time'
                )
                ->addIndex(
                    $installer->getIdxName(Constant::LOCATION_TABLE, array(Constant::LOCATION_TABLE_ID)),
                    Constant::LOCATION_TABLE_ID
                )
                ->setComment('Magenest location Category table');
            $installer->getConnection()->createTable($table);
        }

        $tableName = $installer->getTable(Constant::MAP_TABLE);
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            $table = $installer->getConnection()->newTable($tableName)
                ->addColumn(
                    'map_id',
                    Table::TYPE_INTEGER,
                    null,
                    array(
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ),
                    'Map table primary key'
                )
                ->addColumn(
                    'title',
                    Table::TYPE_TEXT,
                    512,
                    array(
                        'nullable' => false
                    ),
                    'Title'
                )
                ->addColumn('description', Table::TYPE_TEXT, '2M', array(), 'Description')
                ->addColumn(
                    'is_active',
                    Table::TYPE_TEXT,
                    10,
                    array('nullable' => false, 'default' => '1'),
                    'Is Map Active?'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    array('nullable' => false, 'default' => Table::TIMESTAMP_INIT),
                    'Creation Time'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    array('nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE),
                    'Update Time'
                )
                ->setComment('Magenest Map table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $tableName = $installer->getTable(Constant::MAP_LOCATION_TABLE);
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            $table = $installer->getConnection()->newTable($tableName)
                ->addColumn(
                    'map_location_id',
                    Table::TYPE_INTEGER,
                    null,
                    array(
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ),
                    'Map location table primary key'
                )
                ->addColumn(
                    Constant::MAP_TABLE_ID,
                    Table::TYPE_INTEGER,
                    null,
                    array('unsigned' => true),
                    'Reference From Map table'
                )
                ->addColumn(
                    Constant::LOCATION_TABLE_ID,
                    Table::TYPE_INTEGER,
                    null,
                    array('unsigned' => true),
                    'Reference From Location table'
                )
                ->addIndex(
                    $installer->getIdxName(Constant::LOCATION_TABLE, array(Constant::LOCATION_TABLE_ID)),
                    Constant::LOCATION_TABLE_ID
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    array('nullable' => false, 'default' => Table::TIMESTAMP_INIT),
                    'Creation Time'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    array('nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE),
                    'Update Time'
                )
                ->setComment('Magenest Map location table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $tableName = $installer->getTable(Constant::LOCATION_PRODUCT_TABLE);
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            $table = $installer->getConnection()->newTable($tableName)
                ->addColumn(
                    'location_product_id',
                    Table::TYPE_INTEGER,
                    null,
                    array(
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ),
                    'Location Product table primary key'
                )
                ->addColumn(
                    Constant::LOCATION_TABLE_ID,
                    Table::TYPE_INTEGER,
                    null,
                    array('unsigned' => true),
                    'Reference From location table'
                )
                ->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    null,
                    array('unsigned' => true),
                    'Reference From product table'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    array('nullable' => false, 'default' => Table::TIMESTAMP_INIT),
                    'Creation Time'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    array('nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE),
                    'Update Time'
                )
                ->setComment('Magenest location product table');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
