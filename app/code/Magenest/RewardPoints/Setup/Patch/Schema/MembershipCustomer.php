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

namespace Magenest\RewardPoints\Setup\Patch\Schema;

use Magenest\RewardPoints\Api\Data\MembershipCustomerInterface;
use Magenest\RewardPoints\Api\Data\MembershipInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table as Table;
use Magento\Framework\Setup\Patch\PatchInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

class MembershipCustomer implements SchemaPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
    ) {
        /**
         * If before, we pass $setup as argument in install/upgrade function, from now we start
         * inject it with DI. If you want to use setup, you can inject it, with the same way as here
         */
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [\Magenest\RewardPoints\Setup\Patch\Schema\Membership::class];
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
        if (!$this->moduleDataSetup->tableExists(MembershipCustomerInterface::TABLE_NAME)) {
            $table = $this->moduleDataSetup->getConnection()->newTable($this->moduleDataSetup->getTable(MembershipCustomerInterface::TABLE_NAME))
                ->addColumn(
                    MembershipCustomerInterface::CUSTOMER_ID,
                    Table::TYPE_INTEGER,
                    11,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'Membership group ID'
                )->addColumn(
                    MembershipCustomerInterface::MEMBERSHIP_ID,
                    Table::TYPE_INTEGER,
                    10,
                    [
                        'unsigned' => true,
                        'nullable' => false
                    ],
                    'Membership ID'
                )->addColumn(
                    MembershipCustomerInterface::CREATED_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['on_update' => false, 'nullable' => false, 'default' => 'CURRENT_TIMESTAMP'],
                    'Created at'
                )->addColumn(
                    MembershipCustomerInterface::UPDATED_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['on_update' => true, 'nullable' => true],
                    'Updated at'
                )->addIndex('MEMBERSHIP_CUSTOMER_RULE',
                    [MembershipCustomerInterface::CUSTOMER_ID, MembershipCustomerInterface::MEMBERSHIP_ID],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )->addForeignKey('MEMBERSHIP_CUSTOMER_RULE_FK',
                    MembershipCustomerInterface::MEMBERSHIP_ID,
                    $this->moduleDataSetup->getTable(MembershipInterface::TABLE_NAME), MembershipInterface::ENTITY_ID, Table::ACTION_CASCADE
                );

            $this->moduleDataSetup->getConnection()->createTable($table);
        }
        $this->moduleDataSetup->getConnection()->endSetup();
    }
}
