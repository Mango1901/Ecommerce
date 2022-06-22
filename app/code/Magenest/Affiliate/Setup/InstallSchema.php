<?php


namespace Magenest\Affiliate\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package Magenest\Affiliate\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        /** Table magenest_affiliate_account */
        if ($installer->tableExists('magenest_affiliate_account')) {
            $connection->dropTable($installer->getTable('magenest_affiliate_account'));
        }
        $table = $connection->newTable($installer->getTable('magenest_affiliate_account'))
            ->addColumn('account_id', Table::TYPE_INTEGER, null, [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
                'unsigned' => true
            ], 'Account ID')
            ->addColumn('customer_id', Table::TYPE_INTEGER, null, ['nullable => false'], 'Account Customer ID')
            ->addColumn('code', Table::TYPE_TEXT, 255, ['nullable => false'], 'Account Affiliate Code')
            ->addColumn('group_id', Table::TYPE_INTEGER, null, [], 'Account Affiliate Group')
            ->addColumn(
                'balance',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable => false', 'default' => 0.00],
                'Account Balance'
            )
            ->addColumn(
                'holding_balance',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable => false', 'default' => 0.00],
                'Account Holding Balance'
            )
            ->addColumn(
                'total_commission',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable => false', 'default' => 0.00],
                'Account Total Commission'
            )
            ->addColumn(
                'total_paid',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable => false', 'default' => 0.00],
                'Account Total Paid'
            )
            ->addColumn('status', Table::TYPE_INTEGER, null, ['nullable => false'], 'Account Status')
            ->addColumn('email_notification', Table::TYPE_INTEGER, 1, [], 'Account Email Notification')
            ->addColumn('parent', Table::TYPE_INTEGER, null, [], 'Account Referred By')
            ->addColumn('tree', Table::TYPE_TEXT, '64k', [], 'Account Tier Path')
            ->addColumn('withdraw_method', Table::TYPE_TEXT, '64k', [], 'Withdraw method')
            ->addColumn('withdraw_information', Table::TYPE_TEXT, '64k', [], 'Withdraw information')
            ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Account Created At')
            ->setComment('Account Table');
        $connection->createTable($table);

        /** Table magenest_affiliate_group */
        if ($installer->tableExists('magenest_affiliate_group')) {
            $connection->dropTable($installer->getTable('magenest_affiliate_group'));
        }
        $table = $connection->newTable($installer->getTable('magenest_affiliate_group'))
            ->addColumn('group_id', Table::TYPE_INTEGER, null, [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
                'unsigned' => true
            ], 'Group ID')
            ->addColumn('name', Table::TYPE_TEXT, 255, ['nullable => false'], 'Group Name')
            ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Group Created At')
            ->setComment('Group Table');
        $connection->createTable($table);

        /** Table magenest_affiliate_campaign */
        if ($installer->tableExists('magenest_affiliate_campaign')) {
            $connection->dropTable($installer->getTable('magenest_affiliate_campaign'));
        }
        $table = $connection->newTable($installer->getTable('magenest_affiliate_campaign'))
            ->addColumn('campaign_id', Table::TYPE_INTEGER, null, [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
                'unsigned' => true
            ], 'Campaign ID')
            ->addColumn('name', Table::TYPE_TEXT, 255, ['nullable => false'], 'Campaign Name')
            ->addColumn('description', Table::TYPE_TEXT, '64k', [], 'Campaign Description')
            ->addColumn('status', Table::TYPE_INTEGER, null, ['nullable => false'], 'Campaign Status')
            ->addColumn('website_ids', Table::TYPE_TEXT, '64k', ['nullable => false'], 'Campaign Website IDs')
            ->addColumn(
                'affiliate_group_ids',
                Table::TYPE_TEXT,
                '64k',
                ['nullable => false'],
                'Campaign Affiliate Groups'
            )
            ->addColumn('from_date', Table::TYPE_DATETIME, null, [], 'Campaign Active From Date')
            ->addColumn('to_date', Table::TYPE_DATETIME, null, [], 'Campaign Active To Date')
            ->addColumn('display', Table::TYPE_INTEGER, null, ['nullable => false'], 'Campaign Display')
            ->addColumn('sort_order', Table::TYPE_INTEGER, null, [], 'Campaign Sort Order')
            ->addColumn('conditions_serialized', Table::TYPE_TEXT, '64k', [], 'Campaign Conditions Serialized')
            ->addColumn('actions_serialized', Table::TYPE_TEXT, '64k', [], 'Campaign Actions Serialized')
            ->addColumn('commission', Table::TYPE_TEXT, '64k', [], 'Campaign Commission')
            ->addColumn('discount_action', Table::TYPE_TEXT, 255, [], 'Campaign Discount Action')
            ->addColumn('discount_amount', Table::TYPE_DECIMAL, '12,4', ['default' => '0'], 'Campaign Discount Amount')
            ->addColumn(
                'discount_qty',
                Table::TYPE_INTEGER,
                null,
                ['nullable => false', 'default' => '0'],
                'Campaign Discount Qty'
            )
            ->addColumn('discount_step', Table::TYPE_INTEGER, null, ['default' => '0'], 'Campaign Discount Step')
            ->addColumn(
                'discount_description',
                Table::TYPE_TEXT,
                '64k',
                ['nullable => false'],
                'Campaign Discount Description'
            )
            ->addColumn('free_shipping', Table::TYPE_INTEGER, 1, [], 'Campaign Free Shipping')
            ->addColumn('apply_to_shipping', Table::TYPE_INTEGER, 1, [], 'Campaign Apply To Shipping')
            ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Campaign Created At')
            ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, [], 'Campaign Updated At')
            ->setComment('Campaign Table');
        $connection->createTable($table);

        /** Table magenest_affiliate_transaction */
        if ($installer->tableExists('magenest_affiliate_transaction')) {
            $connection->dropTable($installer->getTable('magenest_affiliate_transaction'));
        }
        $table = $connection->newTable($installer->getTable('magenest_affiliate_transaction'))
            ->addColumn('transaction_id', Table::TYPE_INTEGER, null, [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
                'unsigned' => true
            ], 'Transaction ID')
            ->addColumn('account_id', Table::TYPE_INTEGER, null, ['nullable => false'], 'Affiliate Account')
            ->addColumn('customer_id', Table::TYPE_INTEGER, null, ['nullable => false'], 'Customer ID')
            ->addColumn('action', Table::TYPE_TEXT, 255, ['nullable => false'], 'Action')
            ->addColumn('type', Table::TYPE_INTEGER, null, [], 'Action Type')
            ->addColumn('amount', Table::TYPE_DECIMAL, '12,4', [], 'Amount')
            ->addColumn('amount_used', Table::TYPE_DECIMAL, '12,4', [], 'Amount Used')
            ->addColumn('current_balance', Table::TYPE_DECIMAL, '12,4', [], 'Current Balance')
            ->addColumn('status', Table::TYPE_INTEGER, null, ['nullable => false'], 'Status')
            ->addColumn('title', Table::TYPE_TEXT, '64k', ['nullable => false'], 'Title')
            ->addColumn('order_id', Table::TYPE_TEXT, 255, ['nullable => false'], 'Order ID')
            ->addColumn('order_increment_id', Table::TYPE_TEXT, 255, ['nullable => false'], 'Order ID')
            ->addColumn('store_id', Table::TYPE_INTEGER, null, ['nullable => false'], 'Store ID')
            ->addColumn('campaign_id', Table::TYPE_TEXT, 255, [], 'Campaign')
            ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['type' => Table::TYPE_TIMESTAMP, 'default' => Table::TIMESTAMP_INIT], 'Created At')
            ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, [], 'Updated At')
            ->addColumn('holding_to', Table::TYPE_DATETIME, null, [], 'Holding Time')
            ->addColumn('extra_content', Table::TYPE_TEXT, '64k', [], 'Content')
            ->setComment('Transaction Table');
        $connection->createTable($table);

        /** Table magenest_affiliate_withdraw */
        if ($installer->tableExists('magenest_affiliate_withdraw')) {
            $connection->dropTable($installer->getTable('magenest_affiliate_withdraw'));
        }
        $table = $connection->newTable($installer->getTable('magenest_affiliate_withdraw'))
            ->addColumn('withdraw_id', Table::TYPE_INTEGER, null, [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
                'unsigned' => true
            ], 'Withdraw ID')
            ->addColumn('account_id', Table::TYPE_INTEGER, null, [], 'Withdraw Account')
            ->addColumn('customer_id', Table::TYPE_INTEGER, null, ['nullable => false'], 'Withdraw Customer')
            ->addColumn('transaction_id', Table::TYPE_INTEGER, null, ['nullable => false'], 'Withdraw Transaction ID')
            ->addColumn('amount', Table::TYPE_DECIMAL, '12,4', ['nullable => false'], 'Withdraw Amount')
            ->addColumn('fee', Table::TYPE_DECIMAL, '12,4', ['nullable => false'], 'Withdraw Fee')
            ->addColumn(
                'transfer_amount',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable => false'],
                'Withdraw Transaction Amount'
            )
            ->addColumn('status', Table::TYPE_INTEGER, null, ['nullable => false'], 'Withdraw Status')
            ->addColumn('payment_method', Table::TYPE_TEXT, '64k', ['nullable => false'], 'Withdraw Payment Method')
            ->addColumn('payment_details', Table::TYPE_TEXT, '64k', [], 'Withdraw Payment Details')
            ->addColumn('resolved_at', Table::TYPE_TEXT, 255, [], 'Withdraw Resolved At')
            ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Withdraw Created At')
            ->setComment('Withdraw Table');
        $connection->createTable($table);

        //Add discount field in order table
        $affiliate = [
            'affiliate_key' => [
                'type' => Table::TYPE_TEXT,
                'length' => '255',
                'nullable' => true,
                'comment' => 'Affiliate Key'
            ],
            'affiliate_commission' => [
                'type' => Table::TYPE_TEXT,
                'length' => '255',
                'nullable' => true,
                'comment' => 'Affiliate Commission'
            ],
            'affiliate_discount_amount' => [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => true,
                'default' => '0',
                'comment' => 'Affiliate Discount Amount',
            ],
            'base_affiliate_discount_amount' => [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => true,
                'default' => '0',
                'comment' => 'Base Affiliate Discount Amount',
            ],
            'affiliate_campaigns' => [
                'type' => Table::TYPE_TEXT,
                'length' => '255',
                'nullable' => true,
                'comment' => 'Affiliate Campaign Ids'
            ],
        ];

        //add to sales order
        $connection->addColumn($installer->getTable('sales_order'), 'affiliate_key', $affiliate['affiliate_key']);
        $connection->addColumn(
            $installer->getTable('sales_order'),
            'affiliate_commission',
            $affiliate['affiliate_commission']
        );
        $connection->addColumn(
            $installer->getTable('sales_order'),
            'affiliate_discount_amount',
            $affiliate['affiliate_discount_amount']
        );
        $connection->addColumn(
            $installer->getTable('sales_order'),
            'base_affiliate_discount_amount',
            $affiliate['base_affiliate_discount_amount']
        );
        $connection->addColumn(
            $installer->getTable('sales_order'),
            'affiliate_campaigns',
            $affiliate['affiliate_campaigns']
        );

        // add to sales invoice
        $connection->addColumn(
            $installer->getTable('sales_invoice'),
            'affiliate_discount_amount',
            $affiliate['affiliate_discount_amount']
        );
        $connection->addColumn(
            $installer->getTable('sales_invoice'),
            'base_affiliate_discount_amount',
            $affiliate['base_affiliate_discount_amount']
        );

        // add to sales credit memo
        $connection->addColumn(
            $installer->getTable('sales_creditmemo'),
            'affiliate_discount_amount',
            $affiliate['affiliate_discount_amount']
        );
        $connection->addColumn(
            $installer->getTable('sales_creditmemo'),
            'base_affiliate_discount_amount',
            $affiliate['base_affiliate_discount_amount']
        );

        $installer->endSetup();
    }
}
