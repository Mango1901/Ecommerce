<?php

namespace Magenest\Promobar\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'magenest_promobar_bar'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magenest_promobar_bar')
        )->addColumn(
            'promobar_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Promo bar Id'
        )->addColumn(
            'identifier',
            Table::TYPE_TEXT,
            255,
            [],
            'Identifier'
        )->addColumn(
            'title',
            Table::TYPE_TEXT,
            255,
            [],
            'Title'
        )->addColumn(
            'background_image',
            Table::TYPE_TEXT,
            null, [],
            'Background image'
        )->addColumn(
            'edit_background',
            Table::TYPE_TEXT,
            null, [],
            'Edit Background Image'
        )->addColumn(
            'height-pro-bar',
            Table::TYPE_TEXT,
            null, [],
            'Height of Promo Bar'
        )->addColumn(
            'multiple_content',
            Table::TYPE_TEXT,
            '2M',
            [],
            'Content'
        )->addColumn(
            'sticky',
            Table::TYPE_TEXT,
            null,
            [],
            'Sticky'
        )->addColumn(
            'effect',
            Table::TYPE_TEXT,
            255,
            [],
            'Effect'
        )->addColumn(
            'display_type',
            Table::TYPE_TEXT,
            null,
            [],
            'Type Display'
        )->addColumn(
            'time_life',
            Table::TYPE_TEXT,
            null,
            [],
            'Time Life'
        )->addColumn(
            'delay_time',
            Table::TYPE_TEXT,
            null,
            [],
            'Delay Time'
        )->addColumn(
            'delay_content',
            Table::TYPE_TEXT,
            null,
            [],
            'Time delay for each content'
        )->addColumn(
            'allow_closed',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Allow Close'
        )->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 1],
            'Is Active'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Creation Time'
        )->addColumn(
            'update_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Update Time'
        )
//            ->addForeignKey(
//            $installer->getFkName(
//                'magenest_mobile_promobar',
//                'promobar_id',
//                'magenest_promobar_bar',
//                'promobar_id'
//            ),
//            'promobar_id',
//            $installer->getTable('magenest_promobar_bar'),
//            'promobar_id',
//            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
//        )
        ;

        $installer->getConnection()->createTable($table);


        $table = $installer->getConnection()->newTable(
            $installer->getTable('magenest_promobar_button')
        )->addColumn(
            'button_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Button Id'
        )->addColumn(
            'title',
            Table::TYPE_TEXT,
            255,
            [],
            'Title'
        )->addColumn(
            'identifier',
            Table::TYPE_TEXT,
            255,
            [],
            'Identifier'
        )->addColumn(
            'edit_button',
            Table::TYPE_TEXT,
            null, [],
            'Edit Button'
        )->addColumn(
            'background_color',
            Table::TYPE_TEXT,
            null, [],
            'Background Color'
        )->addColumn(
            'background_color_border',
            Table::TYPE_TEXT,
            null, [],
            'Background Color for Border'
        )->addColumn(
            'border_style',
            Table::TYPE_TEXT,
            null, [],
            'Border Style'
        )->addColumn(
            'border_width',
            Table::TYPE_TEXT,
            null, [],
            'Border Width'
        )->addColumn(
            'size',
            Table::TYPE_TEXT,
            null, [],
            'Text Size'
        )->addColumn(
            'content',
            Table::TYPE_TEXT,
            '2M',
            [],
            'Content'
        )->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 1],
            'Is Active'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Creation Time'
        )->addColumn(
            'update_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Update Time'
        );

        $installer->getConnection()->createTable($table);

        $installer->endSetup();

    }
}