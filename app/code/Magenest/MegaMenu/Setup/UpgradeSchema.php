<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */

namespace Magenest\MegaMenu\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * Class UpgradeSchema
 * @package Magenest\MegaMenu\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @inheritdoc
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '2.1.1') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_mega_menu'),
                'custom_css',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => '2M',
                    'nullable' => true,
                    'comment' => 'Custom Css'
                ]
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('magenest_mega_menu'),
                'is_active'
            );
            $table = $installer->getConnection()
                ->newTable($installer->getTable('magenest_menu_preview'))
                ->addColumn('entity_id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'Entity Id')
                ->addColumn('token', Table::TYPE_TEXT, 255, ['nullable' => false], 'Token')
                ->addColumn('data', Table::TYPE_TEXT, '2M', [], 'Data')
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT], 'Created At')
                ->setComment('Mega menu preview');
            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '2.2.0') < 0) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('magenest_menu_label'))
                ->addColumn('label_id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'Label Id')
                ->addColumn('name', Table::TYPE_TEXT, 255, ['nullable' => false], 'Name')
                ->addColumn('to_html', Table::TYPE_TEXT, '64M', [], 'To Html')
                ->setComment('Mega menu label');
            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '2.2.2') < 0) {
            $this->addColumnsTableLabel($setup);
        }

        if (version_compare($context->getVersion(), '2.2.6') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_mega_menu'),
                'menu_top',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => '64',
                    'nullable' => true,
                    'comment' => 'Top Position of Menu Item'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.2.7')) {
            $this->updateMegaMenuGeneralInformation($setup);
        }

		if (version_compare($context->getVersion(), '2.2.18') < 0) {
			$setup->getConnection()->dropColumn($setup->getTable('magenest_mega_menu'), 'menu_event');
		}

        if (version_compare($context->getVersion(), '2.2.14')) {
            $this->addMegaMenuVersioningAndLogTable($setup);
        }

        if (version_compare($context->getVersion(), "2.2.22", "<")) {
            $this->addMenuTypeStaticColumn($setup);
        }

        $installer->endSetup();
    }

    private function addColumnsTableLabel(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable($setup->getConnection()->getTableName('magenest_menu_label')),
            'label_text',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Text Column'

            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable($setup->getConnection()->getTableName('magenest_menu_label')),
            'label_position',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Position'

            ]
        );
    }

    private function updateMegaMenuGeneralInformation(SchemaSetupInterface $setup)
    {
        $columns = [
            'menu_alias' => [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'comment' => "MegeMenu Alias",
                'nullable' => true
            ],
            'event' => [
                'type' => Table::TYPE_TEXT,
                'length' => 127,
                'comment' => "MegeMenu Event",
                'nullable' => true
            ],
            'scroll_to_fixed' => [
                'type' => Table::TYPE_BOOLEAN,
                'length' => null,
                'comment' => "MegeMenu Scroll To Fixed",
                'nullable' => true
            ],
            'customer_group_ids' => [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'comment' => "MegeMenu Customer Group",
                'nullable' => true
            ],
            'mobile_template' => [
                'type' => Table::TYPE_TEXT,
                'length' => 127,
                'comment' => "MegeMenu Mobile Template",
                'nullable' => true
            ],
            'disable_iblocks' => [
                'type' => Table::TYPE_BOOLEAN,
                'length' => null,
                'comment' => "MegeMenu Disable Item Blocks on mobile",
                'nullable' => true
            ],
        ];
        foreach ($columns as $name => $definition) {
            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_mega_menu'),
                $name,
                $definition
            );
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    private function addMegaMenuVersioningAndLogTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('magenest_mega_menu'),
            'current_version',
            [
                'type' => Table::TYPE_INTEGER,
                'length' => null,
                'comment' => "Mega Menu Version",
                'nullable' => true
            ]
        );

        $table = $setup->getConnection()->newTable(
            $setup->getTable('magenest_mega_menu_log')
        )->addColumn(
            'log_id',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true, 'identity' => true],
            'Log ID'
        )->addColumn(
            'menu_id',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Menu ID'
        )->addColumn(
            'version',
            Table::TYPE_TEXT,
            '255',
            ['unsigned' => true],
            'Menu Version'
        )->addColumn(
            'menu_data',
            Table::TYPE_TEXT,
            '256k',
            ['unsigned' => true],
            'Menu Data'
        )->addColumn(
            'menu_structure',
            Table::TYPE_TEXT,
            '256k',
            ['unsigned' => true],
            'Menu Structure'
        )->addColumn(
            'note',
            Table::TYPE_TEXT,
            '64k',
            ['unsigned' => true],
            'Menu Note'
        )->addColumn(
            'update_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Menu Modification Time'
        )->setComment('Menu Log');

        $setup->getConnection()->createTable($table);
    }

    private function addMenuTypeStaticColumn(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable("magenest_menu_entity"),
            'menu_type', [
            'type' => Table::TYPE_INTEGER,
            'length' => 3,
            'nullable' => true,
            'comment' => "Menu Item Type"
        ]);
    }
}
