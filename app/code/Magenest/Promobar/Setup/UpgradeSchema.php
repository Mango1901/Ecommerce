<?php

namespace Magenest\Promobar\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;


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


        /**
         * Create table 'magenest_mobile_promobar'
         */

        $table = $installer->getConnection()->newTable(
            $installer->getTable('magenest_mobile_promobar')
        )->addColumn(
            'mobile_promobar_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Mobile Promo bar Id'
        )->addColumn(
            'promobar_id',
            Table::TYPE_INTEGER,
            null,
            [ 'unsigned' => true, 'nullable' => false],
            'Promobar Id'
        )->addColumn(
            'use_same_config',
            Table::TYPE_INTEGER,
            null,
            [ ],
            'Use Same Config'
        )->addColumn(
            'breakpoint',
            Table::TYPE_TEXT,
            null, [],
            'Breakpoint'
        )->addColumn(
            'mobile_edit_background',
            Table::TYPE_TEXT,
            null, [],
            'Mobile Edit Background Image'
        )->addColumn(
            'mobile_height_pro_bar',
            Table::TYPE_TEXT,
            null, [],
            'Mobile Height of Promo Bar'
        )->addColumn(
            'mobile_multiple_content',
            Table::TYPE_TEXT,
            '2M',
            [],
            'MobileContent'
        )->addForeignKey(
            $installer->getFkName(
                'magenest_promobar_bar',
                'promobar_id',
                'magenest_mobile_promobar',
                'promobar_id'
            ),
            'promobar_id',
            $installer->getTable('magenest_promobar_bar'),
            'promobar_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);

        // Check the versions
        if (version_compare($context->getVersion(), '1.0.1', "<")) {
            // Check if the table already exists
            $tableName = $installer->getTable('magenest_promobar_bar');
            if ($installer->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'store' => [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment'  => 'Store View',
                    ],
                    'sort_order' => [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment'  => 'Sort Order',
                    ],
                    'theme' => [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment'  => 'Theme',
                    ],
                    'pages_display' => [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment'  => 'Pages Display',
                    ],
                    'container_display' => [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment'  => 'Container Display',
                    ],
                    'instance_id_widget' => [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment'  => 'Instance ID of Widget',
                    ],
                ];

                $connection = $installer->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
                $connection->dropColumn($tableName, 'identifier');
            }
        }
        if (version_compare($context->getVersion(), '1.1.1', "<")) {
            // Check if the table already exists
            $tableName = $installer->getTable('magenest_promobar_bar');
            if ($installer->getConnection()->isTableExists($tableName) == true) {
                // Declare data

                $connection = $installer->getConnection();

                $connection->dropColumn($tableName, 'store');
                $connection->dropColumn($tableName, 'sort_order');
                $connection->dropColumn($tableName, 'theme');
                $connection->dropColumn($tableName, 'pages_display');
                $connection->dropColumn($tableName, 'container_display');
                $connection->dropColumn($tableName, 'instance_id_widget');
            }
        }

        if (version_compare($context->getVersion(), '1.0.2', "<")) {
            // Check if the table already exists
            $tableName = $installer->getTable('magenest_promobar_button');
            if ($installer->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'text_color' => [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment'  => 'color for text',
                    ],
                    'hover_color_text' => [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment'  => 'hover color text',
                    ],
                    'hover_color_button' => [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment'  => 'hover color button',
                    ],
                    'hover_color_border' => [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment'  => 'hover color border',
                    ],
                ];

                $connection = $installer->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
                $connection->dropColumn($tableName, 'identifier');
            }
        }

        $installer->endSetup();
    }
}