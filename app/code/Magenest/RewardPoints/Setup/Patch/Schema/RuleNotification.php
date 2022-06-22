<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 02/12/2020 15:59
 */

namespace Magenest\RewardPoints\Setup\Patch\Schema;

use Magenest\RewardPoints\Api\Data\NotificationInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table as Table;
use Magento\Framework\Setup\Patch\PatchInterface;

class RuleNotification implements \Magento\Framework\Setup\Patch\SchemaPatchInterface
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

        if (!$this->moduleDataSetup->tableExists(NotificationInterface::TABLE_NAME)) {
            $table = $this->moduleDataSetup->getConnection()->newTable($this->moduleDataSetup->getTable(NotificationInterface::TABLE_NAME))
                ->addColumn(
                    NotificationInterface::ENTITY_ID,
                    Table::TYPE_INTEGER,
                    11,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'Notification ID'
                )->addColumn(
                    NotificationInterface::RULE_ID,
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false
                    ],
                    'Rule ID'
                )->addColumn(
                    NotificationInterface::NOTIFICATION_STATUS,
                    Table::TYPE_SMALLINT,
                    4,
                    [
                        'nullable' => false
                    ],
                    'Notification Status'
                )->addColumn(
                    NotificationInterface::NOTIFICATION_CONTENT,
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true
                    ],
                    'Notification content'
                )->addColumn(
                    NotificationInterface::NOTIFICATION_DISPLAY_POSITION,
                    Table::TYPE_SMALLINT,
                    4,
                    [
                        'nullable' => false
                    ],
                    'the position show notification'
                )->addColumn(
                    NotificationInterface::NOTIFICATION_DISPLAY_FOR_GUEST,
                    Table::TYPE_SMALLINT,
                    4,
                    [
                        'nullable' => false
                    ],
                    'Notification Status'
                )->addIndex('RULE_NOTIFICATION', NotificationInterface::RULE_ID, ['type' => AdapterInterface::INDEX_TYPE_UNIQUE])
                ->addForeignKey(
                    'RULE_NOTIFICATION_FK', NotificationInterface::RULE_ID,
                    $this->moduleDataSetup->getTable('magenest_rewardpoints_rule'),
                    'id',
                    Table::ACTION_CASCADE
                );
            $this->moduleDataSetup->getConnection()->createTable($table);
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }
}