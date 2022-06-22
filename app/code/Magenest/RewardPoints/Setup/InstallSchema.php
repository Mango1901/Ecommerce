<?php

namespace Magenest\RewardPoints\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table as Table;

/**
 * Class InstallSchema
 * @package Magenest\RewardPoints\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * InstallSchema constructor.
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(\Magento\Framework\App\State $state)
    {
        $this->state = $state;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        try {
            $this->state->emulateAreaCode('adminhtml', function ($setup) {
                $installer = $setup;

                $installer->startSetup();

                $this->createTables($installer);

                $installer->endSetup();
            }, [$setup]);
        } catch (\Exception $exception) {
        }

    }

    /**
     * @param $installer
     */
    public function createTables($installer) {
        $table = $installer->getConnection()->newTable($installer->getTable('magenest_rewardpoints_account'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Customer ID'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Store ID'
            )
            ->addColumn(
                'points_total',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => true, 'default' => '0'],
                'Total Points'
            )
            ->addColumn(
                'points_current',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => true, 'default' => '0'],
                'Current Points'
            )
            ->addColumn(
                'points_spent',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => true, 'default' => '0'],
                'Spent Points'
            )
            ->addColumn(
                'loyalty_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Loyalty ID'
            )->addColumn(
                'store_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Store ID'
            )
            ->setComment('Reward Points Account table');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable($installer->getTable('magenest_rewardpoints_transaction'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Customer ID'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Order ID'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Product ID'
            )
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Rule ID'
            )
            ->addColumn(
                'points_change',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => false],
                'Change Points'
            )
            ->addColumn(
                'points_after',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => false],
                'Current Points'
            )->addColumn(
                'insertion_date',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Insertion Date'
            )
            ->addColumn(
                'comment',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Comment'
            )
            ->setComment('Reward Points Transaction table');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable($installer->getTable('magenest_rewardpoints_rule'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'title',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Rule Title'
            )
            ->addColumn(
                'status',
                Table::TYPE_TEXT,
                10,
                [],
                'Rule Status'
            )
            ->addColumn(
                'rule_type',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Rule Type'
            )
            ->addColumn(
                'customer_group_ids',
                Table::TYPE_TEXT,
                255,
                [],
                'Customer Group Ids'
            )
            ->addColumn(
                'web_ids',
                Table::TYPE_TEXT,
                255,
                [],
                'Website Ids'
            )
            ->addColumn(
                'action_type',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Action Type'
            )
            ->addColumn(
                'conditions_serialized',
                Table::TYPE_TEXT,
                null,
                [],
                'Conditions'
            )->addColumn(
                'condition',
                Table::TYPE_TEXT,
                null,
                [],
                'Condition'
            )
            ->addColumn(
                'from_date',
                Table::TYPE_DATE,
                null,
                ['nullable' => true],
                'From Date'
            )
            ->addColumn(
                'to_date',
                Table::TYPE_DATE,
                null,
                ['nullable' => true],
                'To Date'
            )
            ->addColumn(
                'points',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => true],
                'Points'
            )
            ->addColumn(
                'steps',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => true],
                'Steps'
            )
            ->setComment('Reward Points Rule table');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable($installer->getTable('magenest_rewardpoints_loyalty_level'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Customer Name'
            )
            ->addColumn(
                'discount_rate',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => false],
                'Discount Rate'
            )
            ->addColumn(
                'points_price',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => false],
                'Points Price'
            )
            ->setComment('Reward Points Loyalty level table');

        $installer->getConnection()->createTable($table);
    }

}
