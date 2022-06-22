<?php
namespace Magenest\PhotoReview\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table as Table;
use Magento\Setup\Model\Updater;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magenest_photoreview_photo')
        )->addColumn(
            'photo_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,[
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ],
            'Photo Id'
        )->addColumn(
            'review_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            null,[
                'unsigned' => true,
                'nullable' => false,
                'default' => 0
            ],
            'Review Id'
        )->addColumn(
            'path',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Path of the photo'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Store Id'
        )->addForeignKey(
            $setup->getFkName('magenest_photoreview_photo', 'review_id', 'review', 'review_id'),
            'review_id',
            $setup->getTable('review'),
            'review_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment('magenest_photoreview_photo');

        $installer->getConnection()->createTable($table);

        $magenest_custom_review_detail = $installer->getConnection()->newTable(
            $installer->getTable('magenest_photoreview_detail')
        )->addColumn(
            'custom_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,[
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ],
            'Custom Id'
        )->addColumn(
            'review_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            null,[
                'unsigned' => true,
                'nullable' => false,
                'default' => 0
            ],
            'Review Id'
        )->addColumn(
            'photo_review_is_recommend',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            [],
            'Is Recommend'
        )->addColumn(
            'photo_review_pros',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Photo Review Pros'
        )->addColumn(
            'photo_review_cons',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Photo Review Cons'
        )->addColumn(
            'admin_comment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Admin Comment'
        )->addColumn(
            'photo_external_links',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'External Links'
        )->addColumn(
            'is_purchased',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Is Purchased'
        )->addColumn(
            'rating_sum',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,[
                'nullable' => false,
                'default'  => 0,
            ],
            'Rating Summary'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            [],
            'Order Id'
        )->addForeignKey(
            $setup->getFkName('magenest_photoreview_detail', 'review_id', 'review', 'review_id'),
            'review_id',
            $setup->getTable('review'),
            'review_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment('magenest_photoreview_detail');

        $installer->getConnection()->createTable($magenest_custom_review_detail);

        $magenest_reminder_email = $installer->getConnection()->newTable(
            $installer->getTable('magenest_photoreview_reminder_email')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,[
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ],
            'ID'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Order Id'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Status Send Email'
        )->addColumn(
        'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null, [
                'nullable' => false,
                'default'  => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
            ],
            'Created At'
        )->addColumn(
        'date_send',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Date Send Email'
        )->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Customer Email'
        )->addColumn(
            'customer_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Customer Name'
        )->addColumn(
            'subject',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Email Subject'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Store Id'
        )->addColumn(
            'content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            "Message Content"
        )->setComment('magenest_photoreview_reminder_email');
        $installer->getConnection()->createTable($magenest_reminder_email);

        $installer->endSetup();
    }

}