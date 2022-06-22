<?php

namespace Magenest\RewardPoints\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table as Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            $installer = $setup;
            $installer->startSetup();
            $this->createTableExpired($installer);
            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '1.0.8') < 0) {
            $installer = $setup;
            $installer->startSetup();

            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('magenest_rewardpoints_rule'),
                'rule_configs',
                    [
                        'type' => Table::TYPE_TEXT,
                        'comment' => 'Additional Rule Configurations'
                    ]);

            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '1.0.9', '<')) {
            $installer = $setup;
            $installer->startSetup();
            $this->createTableLifetimeAmount($installer);
            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '1.0.11') < 0) {
            $installer = $setup;
            $installer->startSetup();

            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('magenest_rewardpoints_expired'),
                    'expiry_type',
                    [
                        'type' => Table::TYPE_BOOLEAN,
                        'comment' => 'Expiry Type'
                    ]);

            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '1.1.2') < 0) {
            $installer = $setup;
            $installer->startSetup();

            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('magenest_rewardpoints_expired'),
                    'check_send_email',
                    [
                        'type' => Table::TYPE_TEXT, null,
                        'comment' => 'Check Send Email Expired'
                    ]);

            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '1.2.0') < 0) {
            $installer = $setup;
            $installer->startSetup();
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('magenest_rewardpoints_expired'),
                    'check_referral',
                    [
                        'type' => Table::TYPE_TEXT, null,
                        'comment' => 'Check Send Email referral'
                    ]);
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('magenest_rewardpoints_transaction'),
                    'expired_id',
                    [
                        'type' => Table::TYPE_TEXT, null,
                        'comment' => 'Expired ID'
                    ]);
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('magenest_rewardpoints_transaction'),
                    'point_refund',
                    [
                        'type' => Table::TYPE_FLOAT, null,
                        'comment' => 'Point Refund'
                    ]);
            $this->createTableReferralCode($installer);
            $this->createTableReferralPoints($installer);
            $this->createTableReferralCoupon($installer);
            $this->createTableShareReferral($installer);
            $installer->endSetup();
        }
    }

    public function createTableReferralCode($installer){
        /** @var SchemaSetupInterface $installer */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magenest_rewardpoints_referral_code')
        )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Customer ID'
            )->addColumn(
                'referral_code',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Referral Code'
            )->addColumn(
                'code',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Referral Code'
            )->setComment('Reward Points Referral Code table');
        $installer->getConnection()->createTable($table);
    }

    public function createTableReferralPoints($installer){
        /** @var SchemaSetupInterface $installer */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magenest_rewardpoints_referral_points')
        )
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Rule ID'
            )
            ->addColumn(
                'points_referring',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                [],
                'Points for the referre'
            )
            ->addColumn(
                'points_referred',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                [],
                'Points for the referred'
            )
            ->addForeignKey(
                $installer->getFkName('magenest_rewardpoints_referral_points', 'rule_id', 'magenest_rewardpoints_rule', 'id'),
                'rule_id',
                $installer->getTable('magenest_rewardpoints_rule'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);
    }

    public function createTableReferralCoupon($installer){
        /** @var SchemaSetupInterface $installer */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magenest_rewardpoints_referral_coupons')
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
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Customer Id'
        )->addColumn(
            'referral_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Referral Code'
        )->addColumn(
            'coupon',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Coupon'
        )->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Referred/Referrer'
        )->addColumn(
            'comment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Comment'
        )->setComment('Create new magenest_rewardpoints_referral_coupons table ');
        $installer->getConnection()->createTable($table);
    }

    public function createTableShareReferral($installer){
        /** @var SchemaSetupInterface $installer */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magenest_rewardpoints_my_referral')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,[
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true
        ],
            'Id'
        )->addColumn(
            'email_referred',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Email of the Referred'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Customer Id'
        )->addColumn(
            'customer_referred_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Customer Referred Id'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Status'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,[
            'nullable' => false,
            'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
        ],
            'Created At'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,[
            'nullable' => false,
            'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
        ],
            'Updated At'
        )->setComment('create new magenest_rewardpoints_my_referral table');
        $installer->getConnection()->createTable($table);
    }

    public function createTableExpired($installer){
        $table = $installer->getConnection()->newTable($installer->getTable('magenest_rewardpoints_expired'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Customer ID'
            )->addColumn(
                'transaction_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Transaction ID'
            )->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Order ID'
            )->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Product ID'
            )->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Rule ID'
            )->addColumn(
                'points_change',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => false],
                'Change Points'
            )->addColumn(
                'insertion_date',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Insertion Date'
            )->addColumn(
                'expired_date',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Expired Date'
            )->addColumn(
                'status',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Status (expired, available, or used)'
            )->addColumn(
                'notified',
                Table::TYPE_SMALLINT,
                4,
                ['nullable' => false,
                    'default'  => '0'],
                'Is notification yet'
            )->setComment('Reward Points Expired table');

        $installer->getConnection()->createTable($table);
    }

    public function createTableLifetimeAmount($installer){
        $table = $installer->getConnection()
            ->newTable($installer->getTable('magenest_rewardpoints_lifetime_amount'))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => false, 'unsigned' => true, 'nullable' => false, 'primary' => false],
                'Customer ID'
            )
            ->addColumn(
                'ordered_amount',
                Table::TYPE_DECIMAL,
                '12,4',
                [],
                'Ordered Amount'
            )
            ->addColumn(
                'order_ids',
                Table::TYPE_TEXT,
                null,
                [],
                'Order Ids'
            )
            ->addColumn(
                'ordered_amount',
                Table::TYPE_DECIMAL,
                '12,4',
                [],
                'Ordered Amount'
            )
            ->addColumn(
                'order_ids',
                Table::TYPE_TEXT,
                null,
                [],
                'Order Ids'
            )
            ->addColumn(
                'invoiced_amount',
                Table::TYPE_DECIMAL,
                '12,4',
                [],
                'Invoiced Amount'
            )
            ->addColumn(
                'invoice_ids',
                Table::TYPE_TEXT,
                null,
                [],
                'Invoice Ids'
            )
            ->addForeignKey(
                $installer->getFkName('magenest_rewardpoints_lifetime_amount', 'customer_id', 'customer_entity', 'entity_id'),
                'customer_id',
                $installer->getTable('customer_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Reward Points Lifetime Amount');
        $installer->getConnection()->createTable($table);
    }
}
