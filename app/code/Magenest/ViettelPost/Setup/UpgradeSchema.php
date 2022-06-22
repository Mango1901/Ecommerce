<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\ViettelPost\Setup;

use Magenest\ViettelPost\Model\District;
use Magenest\ViettelPost\Model\Province;
use Magenest\ViettelPost\Model\ViettelOrder;
use Magenest\ViettelPost\Model\ViettelWebhook;
use Magenest\ViettelPost\Model\Wards;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table as Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Magenest\Directory\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->createAddressInfoTable($installer);
        }
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->createViettelPostOrderTable($installer);
            $this->createViettelPostWebhookTable($installer);
        }
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->createViettelPostOrderShippingLebelUrlTable($installer);
        }
        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $this->changePrimaryKeyViettelPostTable($installer);
        }

        $installer->endSetup();
    }

    private function changePrimaryKeyViettelPostTable($installer){
        $installer->getConnection()->changeColumn(
            $installer->getTable(ViettelOrder::TABLE_NAME),
            'order_id',
            'shipment_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => null,
                'nullable' => false,
                'primary' => true,
                'unsigned' => true,
                'comment' => 'Shipment Id',
            ]
        );

        $installer->getConnection()->changeColumn(
            $installer->getTable(ViettelWebhook::TABLE_NAME),
            'order_id',
            'shipment_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => null,
                'nullable' => false,
                'primary' => true,
                'unsigned' => true,
                'comment' => 'Shipment Id',
            ]
        );
    }

    private function createViettelPostOrderShippingLebelUrlTable($installer){
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable(ViettelOrder::TABLE_NAME),
            'shipping_label_url',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => '200',
                'comment' => 'Shipping Label'
            ]
        );
    }

    private function createViettelPostOrderTable($installer){
        $connection = $installer->getConnection();
        if (!$installer->tableExists($installer->getTable(ViettelOrder::TABLE_NAME))) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable(ViettelOrder::TABLE_NAME))
                ->addColumn('order_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'primary' => true, 'unsigned' => true], 'Order Id')
                ->addColumn('order_number', Table::TYPE_TEXT, '64', ['nullable' => false], 'Order number')
                ->addColumn('money_collection', Table::TYPE_INTEGER, null, [], 'Money Collection')
                ->addColumn('exchange_weight', Table::TYPE_INTEGER, null, [], 'Exchange Weight')
                ->addColumn('money_total', Table::TYPE_INTEGER, null, [], 'Money Total')
                ->addColumn('money_total_fee', Table::TYPE_INTEGER, null, [], 'money_total_fee')
                ->addColumn('money_fee', Table::TYPE_INTEGER, null, [], 'money_fee')
                ->addColumn('money_collection_fee', Table::TYPE_INTEGER, null, [], 'money_collection_fee')
                ->addColumn('money_other_fee', Table::TYPE_INTEGER, null, [], 'money_other_fee')
                ->addColumn('money_vas', Table::TYPE_INTEGER, null, [], 'money_vas')
                ->addColumn('money_vat', Table::TYPE_INTEGER, null, [], 'money_vat')
                ->addColumn('kpi_ht', Table::TYPE_INTEGER, null, [], 'kpi_ht')
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT], 'Created At')
                ->addIndex(
                    $installer->getIdxName(ViettelOrder::TABLE_NAME, array('order_number')),
                    'order_number'
                )
                ->setComment('ViettelPost Order');
            $connection->createTable($table);
        }
    }

    private function createViettelPostWebhookTable($installer){
        $connection = $installer->getConnection();
        if (!$installer->tableExists($installer->getTable(ViettelWebhook::TABLE_NAME))) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable(ViettelWebhook::TABLE_NAME))
                ->addColumn('id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true], 'Id')
                ->addColumn('order_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'unsigned' => true], 'Order id')
                ->addColumn('order_number', Table::TYPE_TEXT, '64', ['nullable' => false], 'Order number')
                ->addColumn('order_reference', Table::TYPE_TEXT, '64', [], 'Order reference')
                ->addColumn('order_statusdate', Table::TYPE_TEXT, '64', [], 'order_statusdate')
                ->addColumn('order_status', Table::TYPE_INTEGER, null, [], 'order_status')
                ->addColumn('status_name', Table::TYPE_TEXT, '64', [], 'status_name')
                ->addColumn('note', Table::TYPE_TEXT, '500', [], 'note')
                ->addColumn('money_collection', Table::TYPE_INTEGER, null, [], 'money_collection')
                ->addColumn('money_feecod', Table::TYPE_INTEGER, null, [], 'money_feecod')
                ->addColumn('money_total', Table::TYPE_INTEGER, null, [], 'money_total')
                ->addColumn('expected_delivery', Table::TYPE_TEXT, '200', [], 'expected_delivery')
                ->addColumn('product_weight', Table::TYPE_INTEGER, null, [], 'product_weight')
                ->addColumn('order_service', Table::TYPE_TEXT, '64', [], 'order_service')
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT], 'Created At')
                ->addIndex(
                    $installer->getIdxName(ViettelWebhook::TABLE_NAME, array('order_id')),
                    'order_id'
                )
                ->addIndex(
                    $installer->getIdxName(ViettelWebhook::TABLE_NAME, array('order_number')),
                    'order_number'
                )
                ->setComment('ViettelPost Order');
            $connection->createTable($table);
        }
    }

    private function createAddressInfoTable($installer){
        $connection = $installer->getConnection();
        if (!$installer->tableExists($installer->getTable(Province::TABLE_NAME))) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable(Province::TABLE_NAME))
                ->addColumn('province_id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'Province Id')
                ->addColumn('province_code', Table::TYPE_TEXT, '64', ['nullable' => false], 'Province Code')
                ->addColumn('province_name', Table::TYPE_TEXT, '64', ['nullable' => false], 'Province Name')
                ->setComment('Province Table');
            $connection->createTable($table);
        }

        /** Create District table */
        if (!$installer->tableExists($installer->getTable(District::TABLE_NAME))) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable(District::TABLE_NAME))
                ->addColumn('district_id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'District Id')
                ->addColumn('province_id', Table::TYPE_INTEGER, null, ['nullable' => false], 'Province Id')
                ->addColumn('district_value', Table::TYPE_INTEGER, '64', ['nullable' => false], 'District Value')
                ->addColumn('district_name', Table::TYPE_TEXT, '64', ['nullable' => false], 'District Name')
                ->addIndex(
                    $installer->getIdxName(District::TABLE_NAME, array('province_id')),
                    'province_id'
                )
                ->setComment('District Table');
            $connection->createTable($table);
        }

        /** Create Wards table */
        if (!$installer->tableExists($installer->getTable(Wards::TABLE_NAME))) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable(Wards::TABLE_NAME))
                ->addColumn('wards_id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'Wards Id')
                ->addColumn('district_id', Table::TYPE_INTEGER, null, ['nullable' => false], 'District Id')
                ->addColumn('wards_name', Table::TYPE_TEXT, '64', ['nullable' => false], 'Wards Name')
                ->addIndex(
                    $installer->getIdxName(Wards::TABLE_NAME, array('district_id')),
                    'district_id'
                )
                ->setComment('Ward Table');

            $connection->createTable($table);
        }
    }
}
