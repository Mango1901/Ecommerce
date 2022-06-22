<?php

    namespace Magenest\MapList\Setup;

    use Magento\Framework\DB\Ddl\Table;
    use Magento\Framework\Setup\UpgradeSchemaInterface;
    use Magento\Framework\Setup\ModuleContextInterface;
    use Magento\Framework\Setup\SchemaSetupInterface;
    use Magenest\MapList\Helper\Constant;
    use function PHPSTORM_META\type;

    /**
     * Class InstallSchema
     * Get the new tables up and running
     *
     *
     */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        // Check the versions
        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            // Check if the table already exists
            $tableName = $installer->getTable(Constant::LOCATION_TABLE);
            if ($installer->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = array(
                    'country' => array(
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment'  => 'Country',
                    ),
                    'state_province' => array(
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment'  => 'State/Province',
                    ),
                    'city' => array(
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment'  => 'City',
                    ),
                    'zip' => array(
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment'  => 'zip',
                    ),
                );

                $connection = $installer->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.2.0') < 0) {
            $tableName = $installer->getTable(Constant::LOCATION_TABLE);
            if ($installer->getConnection()->isTableExists($tableName) == true) {
                $columns = array(
                    'is_use_default_opening_hours' => array(
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Use Default Opening Hours'
                    ),
                    'opening_hours' => array(
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Opening Hours Json'
                    ),
                    'special_date' => array(
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => "Special Date Json"
                    )
                );
                $connection = $installer->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.2.0') < 0) {
            // Check if the table already exists
            $tableName = $installer->getTable(Constant::LOCATION_TABLE);
            if ($installer->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = array(
                    'gallery' => array(
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment'  => 'Gallery',
                    ),
                );

                $connection = $installer->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.2.0') < 0) {
            $installer = $setup;
            $installer->startSetup();
            $connection = $installer->getConnection();
            $table = $installer->getConnection()->newTable($installer->getTable(Constant::LOCATION_GALLERY_TABLE))->addColumn(
                'location_gallery_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
                    ),
                'Gallery ID'
            )->addColumn(
                'name_image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                array('nullable' => false),
                'Name Image'
            );
            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.2.0') < 0) {
            // Check if the table already exists
            $tableName = $installer->getTable(Constant::LOCATION_GALLERY_TABLE);
            if ($installer->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = array(
                    'type_image' => array(
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment'  => 'Type Icon (1) & Type Gallery(2)',
                    ),
                );

                $connection = $installer->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.2.0') < 0) {
            // Check if the table already exists
            $tableName = $installer->getTable(Constant::MAP_TABLE);
            if ($installer->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = array(
                    'config_title' => array(
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment'  => 'Config Title for Map',
                    ),
                );

                $connection = $installer->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.3.1') < 0) {
            $installer = $setup;
            $installer->startSetup();
            $connection = $installer->getConnection();
            $table = $installer->getConnection()->newTable($installer->getTable(Constant::STORE_PRODUCT_TABLE))->addColumn(
                Constant::STORE_PRODUCT_TABLE_ID,
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
                    ),
                'Store Product ID'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array('unsigned' => true,'nullable' => false),
                'Product ID'
            )->addColumn(
                'location_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array('unsigned' => true,'nullable' => false),
                'Location ID'
            )->addIndex(
                $installer->getIdxName(Constant::STORE_PRODUCT_TABLE, array('product_id')),
                array('product_id')
            )->addIndex(
                $installer->getIdxName(Constant::STORE_PRODUCT_TABLE, array('location_id')),
                array('location_id')
            )->addForeignKey(
                $installer->getFkName(
                    'magenest_maplist_store_product',
                    'product_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'product_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    'magenest_maplist_store_product',
                    'location_id',
                    'magenest_maplist_location',
                    'location_id'
                ),
                'location_id',
                $installer->getTable('magenest_maplist_location'),
                'location_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.3.2') < 0) {
            // Check if the table already exists
            $tableName = $installer->getTable(Constant::LOCATION_TABLE);
            if ($installer->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = array(
                    'store' => array(
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'comment'  => 'Store view',
                    ),
                );

                $connection = $installer->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.3.3') < 0) {
            // Check if the table already exists
            if ($installer->getConnection()->isTableExists($tableName = $installer->getTable(Constant::CATEGORY_TABLE)) == true) {
                $connection = $installer->getConnection();
                $connection->dropTable($connection->getTableName(Constant::CATEGORY_TABLE));
            }

            if ($installer->getConnection()->isTableExists($tableName = $installer->getTable(Constant::LOCATION_CATEGORY_TABLE)) == true) {
                $connection = $installer->getConnection();
                $connection->dropTable($connection->getTableName(Constant::LOCATION_CATEGORY_TABLE));
            }

            if ($installer->getConnection()->isTableExists($tableName = $installer->getTable(Constant::MAP_TABLE)) == true) {
                $connection = $installer->getConnection();
                $connection->dropTable($connection->getTableName(Constant::MAP_TABLE));
            }

            if ($installer->getConnection()->isTableExists($tableName = $installer->getTable(Constant::MAP_LOCATION_TABLE)) == true) {
                $connection = $installer->getConnection();
                $connection->dropTable($connection->getTableName(Constant::MAP_LOCATION_TABLE));
            }
        }

        if (version_compare($context->getVersion(), '1.3.4') < 0) {
            $tableName = $installer->getTable(Constant::LOCATION_TABLE);
            if ($installer->getConnection()->isTableExists($tableName) == true) {
                $connection = $installer->getConnection();
                $connection->changeColumn(
                    $tableName,
                    'latitude',
                    'latitude',
                    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'nullable' => false),
                    'Latitude'
                );
                $connection->changeColumn(
                    $tableName,
                    'longitude',
                    'longitude',
                    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'nullable' => false),
                    'Longitude'
                );
            }
        }

//        create table magenest_maplist_holiday
        if (version_compare($context->getVersion(), '1.4.3') < 0){
            $tableName = $installer->getTable('magenest_maplist_holiday');
            if ($setup->tableExists($tableName) !== true) {

                $table = $installer->getConnection()->newTable(
                    $installer->getTable('magenest_maplist_holiday')
                )->addColumn(
                    'holiday_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Holiday Id'
                )->addColumn(
                    'holiday_name',
                    Table::TYPE_TEXT,
                    255,
                    ['default' => '', 'nullable' => false],
                    'Holiday Name'
                )->addColumn(
                    'status',
                    Table::TYPE_BOOLEAN,
                    null,
                    ['nullable' => false],
                    'Status'
                )->addColumn(
                    'date',
                    Table::TYPE_DATE,
                    null,
                    ['nullable' => false],
                    'Date'
                )->addColumn(
                    'holiday_date_to',
                    Table::TYPE_DATE,
                    null,
                    ['nullable' => true],
                    'Holiday Date To'
                )->addColumn(
                    'comment',
                    Table::TYPE_TEXT,
                    255,
                    ['default' => null, 'nullable' => true],
                    'Comment'
                )->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    array('nullable' => false, 'default' => Table::TIMESTAMP_INIT),
                    'Create Time'
                )->addColumn(
                        'updated_at',
                        Table::TYPE_TIMESTAMP,
                        null,
                        array('nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE),
                        'Update Time'
                    )->setComment('Holiday');

                $installer->getConnection()->createTable($table);
            }
        }

//        create table magenest_maplist_holiday_location
        if (version_compare($context->getVersion(), '1.4.4') < 0){
            $tableName = $installer->getTable('magenest_maplist_holiday_location');
            if ($setup->tableExists($tableName) !== true) {

                $table = $installer->getConnection()->newTable(
                    $installer->getTable('magenest_maplist_holiday_location')
                )->addColumn(
                    'holiday_location_id',
                    Table::TYPE_INTEGER,
                    null,
                    array(
                        'auto_increment'=>true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary'  => true,
                    ),
                    'Holiday Location ID'
                )
                ->addColumn(
                'holiday_id',
                Table::TYPE_INTEGER,
                null,
                array('unsigned' => true,'nullable' => false),
                'Holiday ID'
            )->addColumn(
                'location_id',
                Table::TYPE_INTEGER,
                null,
                    array('unsigned' => true,'nullable' => false),
                'Location ID'
            )->addIndex(
                    $installer->getIdxName(Constant::HOLIDAY_LOCATION_TABLE, array('holiday_id')),
                    array('holiday_id')
            )->addIndex(
                    $installer->getIdxName(Constant::HOLIDAY_LOCATION_TABLE, array('location_id')),
                    array('location_id')
            )->addForeignKey(
                    $installer->getFkName(
                        'magenest_maplist_holiday_location',
                        'holiday_id',
                        'magenest_maplist_holiday',
                        'holiday_id'
                    ),
                    'holiday_id',
                    $installer->getTable('magenest_maplist_holiday'),
                    'holiday_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->addForeignKey(
                    $installer->getFkName(
                        'magenest_maplist_holiday_location',
                        'location_id',
                        'magenest_maplist_location',
                        'location_id'
                    ),
                    'location_id',
                    $installer->getTable('magenest_maplist_location'),
                    'location_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->setComment('Holiday location table');
            $installer->getConnection()->createTable($table);
            }
        }

        // Add SEO
        if (version_compare($context->getVersion(), '1.4.5') < 0) {
            $tableName = $installer->getTable(Constant::LOCATION_TABLE);
            if ($installer->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = array(
                    'meta_title' => array(
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => true,
                        'comment' => 'Meta Title'
                    ),
                    'meta_keywords' => array(
                        'type' => Table::TYPE_TEXT,
                        'length' => '64k',
                        'nullable' => true,
                        'comment' => 'Meta Keywords'
                    ),
                    'meta_description' => array(
                        'type' => Table::TYPE_TEXT,
                        'length' => '64k',
                        'nullable' => true,
                        'comment' => 'Meta Description'
                    ),
                    'enable_seo' => array(
                        'type' => Table::TYPE_BOOLEAN,
                        'length' => '1',
                        'nullable' => true,
                        'comment' => 'Enable Seo'
                    )
                );

                $connection = $installer->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        //Add location attribute
        if (version_compare($context->getVersion(), '1.4.6') < 0) {
            $tableName = $installer->getTable(Constant::LOCATION_TABLE);
            if ($installer->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = array(
                    'parking' => array(
                        'type' => Table::TYPE_BOOLEAN,
                        'length' => '1',
                        'nullable' => true,
                        'comment' => 'Parking'
                    ),
                    'atm' => array(
                        'type' => Table::TYPE_BOOLEAN,
                        'length' => '1',
                        'nullable' => true,
                        'comment' => 'ATM'
                    ),
                    'new_arrivals' => array(
                        'type' => Table::TYPE_TEXT,
                        'length' => '100',
                        'nullable' => true,
                        'comment' => 'New Arrivals'
                    ),
                    'payment_methods' => array(
                        'type' => Table::TYPE_TEXT,
                        'length' => '100',
                        'nullable' => true,
                        'comment' => 'Payment Methods'
                    ),
                    'brands' => array(
                        'type' => Table::TYPE_TEXT,
                        'length' => '255',
                        'nullable' => true,
                        'comment' => 'Brands'
                    )
                );

                $connection = $installer->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }

            $tableName = $installer->getTable(Constant::BRAND_TABLE);
            if ($setup->tableExists($tableName) !== true) {

                $table = $installer->getConnection()->newTable(
                    $installer->getTable(Constant::BRAND_TABLE)
                )->addColumn(
                    'brand_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['auto_increment' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Brand ID'
                )->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Brand Name'
                )->addColumn(
                    'logo',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Brand Logo'
                )->addColumn(
                    'description',
                    Table::TYPE_TEXT,
                    '64k',
                    array('nullable' => false),
                    'Brand Description'
                )->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Brand Created At'
                )->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                    'Brand Update At'
                )->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '1'],
                    'Status'
                )->setComment('Brand table');
                $installer->getConnection()->createTable($table);
            }
        }
        $installer->endSetup();
    }
}
