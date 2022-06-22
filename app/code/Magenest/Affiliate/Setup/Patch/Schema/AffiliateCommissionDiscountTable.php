<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 02/12/2020 16:04
 */

namespace Magenest\Affiliate\Setup\Patch\Schema;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table as Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

class AffiliateCommissionDiscountTable implements SchemaPatchInterface
{
    const TABLE_NAME = 'magenest_affiliate_commission_discount';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     * @throws \Zend_Db_Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        if (!$this->moduleDataSetup->tableExists(self::TABLE_NAME)) {
            $table = $this->moduleDataSetup->getConnection()->newTable($this->moduleDataSetup->getTable(self::TABLE_NAME))
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    11,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'Entity ID'
                )->addColumn(
                    'affiliate_account_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false
                    ],
                    'Affiliate Account ID'
                )->addColumn(
                    'campaign_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false
                    ],
                    'Campaign ID'
                )->addColumn(
                    'total_value',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    [
                        'default' => '0'
                    ],
                    'Total Commission Value'
                )->addColumn(
                    'type',
                    Table::TYPE_SMALLINT,
                    null,
                    [],
                    'Action Type'
                )->addColumn(
                    'total_value_second',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    [
                        'default' => '0'
                    ],
                    'Total Commission Value Second'
                )->addColumn(
                    'type_second',
                    Table::TYPE_SMALLINT,
                    null,
                    [],
                    'Action Type Second'
                )->addColumn(
                    'customer_value',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    [
                        'default' => '0'
                    ],
                    'Affiliate Value'
                )->addColumn(
                    'customer_value_second',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    [
                        'default' => '0'
                    ],
                    'Affiliate Value Second'
                )->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['on_update' => false, 'nullable' => false, 'default' => 'CURRENT_TIMESTAMP'],
                    'Created at'
                )->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['on_update' => true, 'nullable' => true],
                    'Updated at'
                )->addIndex('AFFILIATE_ACCOUNT_CAMPAIGN_RULE',
                    ['affiliate_account_id', 'campaign_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )->addForeignKey(
                    'M_A_C_D_AFFILIATE_ACCOUNT_ID_M_A_A_ACCOUNT_ID',
                    'affiliate_account_id',
                    $this->moduleDataSetup->getTable('magenest_affiliate_account'),
                    'account_id',
                    Table::ACTION_CASCADE
                )->addForeignKey(
                    'M_A_C_D_CAMPAIGN_ID_M_A_C_CAMPAIGN_ID',
                    'campaign_id',
                    $this->moduleDataSetup->getTable('magenest_affiliate_campaign'),
                    'campaign_id',
                    Table::ACTION_CASCADE
                );

            $this->moduleDataSetup->getConnection()->createTable($table);
        }
        $this->moduleDataSetup->getConnection()->endSetup();
    }
}
