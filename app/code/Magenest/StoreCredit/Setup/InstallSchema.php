<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package Magenest\StoreCredit\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        if (!$setup->tableExists('magenest_store_credit_customer')) {
            $table = $connection
                ->newTable($setup->getTable('magenest_store_credit_customer'))
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => 0],
                    'Customer Id'
                )
                ->addColumn(
                    'mp_credit_balance',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false, 'default' => 0],
                    'Store Credit Balance'
                )
                ->addColumn(
                    'mp_credit_notification',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false],
                    'Notification Update'
                )
                ->addForeignKey(
                    $setup->getFkName('magenest_store_credit_customer', 'customer_id', 'customer_entity', 'entity_id'),
                    'customer_id',
                    $setup->getTable('customer_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Magenest Store Credit Customer');
            $connection->createTable($table);
        }

        if (!$setup->tableExists('magenest_store_credit_transaction')) {
            $table = $setup->getConnection()
                ->newTable($setup->getTable('magenest_store_credit_transaction'))
                ->addColumn(
                    'transaction_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                    'Store Credit Id'
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Credit Id'
                )
                ->addColumn('order_id', Table::TYPE_INTEGER, null, ['nullable' => false], 'Order Id')
                ->addColumn('title', Table::TYPE_TEXT, 255, ['nullable' => false], 'Title')
                ->addColumn('status', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => 0], 'Status')
                ->addColumn('action', Table::TYPE_TEXT, 255, ['nullable' => false], 'Action')
                ->addColumn('amount', Table::TYPE_DECIMAL, '12,4', ['nullable' => false, 'default' => 0], 'Amount')
                ->addColumn('balance', Table::TYPE_DECIMAL, '12,4', ['nullable' => false, 'default' => 0], 'Balance')
                ->addColumn('customer_note', Table::TYPE_TEXT, '1M', [], 'Customer Note')
                ->addColumn('admin_note', Table::TYPE_TEXT, '1M', [], 'Admin Note')
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->addForeignKey(
                    $setup->getFkName(
                        'magenest_store_credit_transaction',
                        'customer_id',
                        'customer_entity',
                        'entity_id'
                    ),
                    'customer_id',
                    $setup->getTable('customer_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Magenest Store Credit Transaction');
            $connection->createTable($table);
        }

        $setup->endSetup();
    }
}
