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

use Magenest\RewardPoints\Api\Data\MembershipInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\PatchInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

class Membership implements SchemaPatchInterface
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

        if (!$this->moduleDataSetup->tableExists(MembershipInterface::TABLE_NAME)) {
            $table = $this->moduleDataSetup->getConnection()->newTable($this->moduleDataSetup->getTable(MembershipInterface::TABLE_NAME))
                ->addColumn(
                    MembershipInterface::ENTITY_ID,
                    Table::TYPE_INTEGER,
                    10,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true
                    ],
                    'Membership ID'
                )->addColumn(
                    MembershipInterface::GROUP_NAME,
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false
                    ],
                    'Membership Name'
                )->addColumn(
                    MembershipInterface::GROUP_CODE,
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false
                    ],
                    'Membership Code'
                )->addColumn(
                    MembershipInterface::GROUP_DESCRIPTION,
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true
                    ],
                    'Membership description'
                )->addColumn(
                    MembershipInterface::GROUP_STATUS,
                    Table::TYPE_SMALLINT,
                    4,
                    [
                        'nullable' => false
                    ],
                    'Membership status'
                )->addColumn(
                    MembershipInterface::GROUP_CONDITION_TYPE,
                    Table::TYPE_SMALLINT,
                    4,
                    [
                        'nullable' => false
                    ],
                    'Condition type to reach membership'
                )->addColumn(
                    MembershipInterface::GROUP_CONDITION_VALUE,
                    Table::TYPE_SMALLINT,
                    4,
                    [
                        'nullable' => false
                    ],
                    'value of condition to reach membership'
                )->addColumn(
                    MembershipInterface::TIER_LOGO,
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true
                    ],
                    'Membership logo'
                )->addColumn(
                    MembershipInterface::ADDITIONAL_EARNING_POINT,
                    Table::TYPE_SMALLINT,
                    4,
                    [
                        'nullable' => false
                    ],
                    'Allow additional earning point'
                )->addColumn(
                    MembershipInterface::ADDED_VALUE_TYPE,
                    Table::TYPE_SMALLINT,
                    4,
                    [
                        'nullable' => true
                    ],
                    'Additional earning point type'
                )->addColumn(
                    MembershipInterface::ADDED_VALUE_AMOUNT,
                    Table::TYPE_INTEGER,
                    11,
                    [
                        'nullable' => true
                    ],
                    'Additional earning point value'
                )->addColumn(
                    MembershipInterface::GROUP_BENEFIT,
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true
                    ],
                    'Membership benefit'
                )->addColumn(
                    MembershipInterface::GROUP_REQUIREMENTS,
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true
                    ],
                    'Membership requirement'
                )->addColumn(
                    MembershipInterface::SORT_ORDER,
                    Table::TYPE_SMALLINT,
                    5,
                    [
                        'nullable' => false
                    ],
                    'Membership sort order'
                )->addColumn(
                    MembershipInterface::CREATE_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['on_update' => false, 'nullable' => false, 'default' => 'CURRENT_TIMESTAMP'],
                    'Created at'
                )->addIndex('MEMBERSHIP_NAME',
                    [MembershipInterface::GROUP_CODE, MembershipInterface::GROUP_NAME],
                    ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
                );

            $this->moduleDataSetup->getConnection()->createTable($table);
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }
}