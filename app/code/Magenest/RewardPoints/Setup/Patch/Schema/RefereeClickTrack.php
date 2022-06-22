<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * User: leo
 * Date: 21/06/2022
 * Time: 11:44
 */
declare(strict_types=1);

namespace Magenest\RewardPoints\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table as Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Zend_Db_Exception;

class RefereeClickTrack implements SchemaPatchInterface
{
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
     * @throws Zend_Db_Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        if (!$this->moduleDataSetup->tableExists('referee_click_track')) {
            $table = $this->moduleDataSetup->getConnection()->newTable($this->moduleDataSetup->getTable('referee_click_track'))
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
                    'customer_id',
                    Table::TYPE_INTEGER,
                    10,
                    [
                        'unsigned' => true,
                        'nullable' => false
                    ],
                    'Membership ID'
                )->addColumn(
                    'apply_customer_id',
                    Table::TYPE_INTEGER,
                    10,
                    [
                        'unsigned' => true,
                        'nullable' => false
                    ],
                    'Membership ID'
                )->addColumn(
                    'condition_type',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'unsigned' => true,
                        'nullable' => false
                    ],
                    'Condition Type'
                )->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['on_update' => false, 'nullable' => false, 'default' => 'CURRENT_TIMESTAMP'],
                    'Created at'
                );

            $this->moduleDataSetup->getConnection()->createTable($table);
        }
        $this->moduleDataSetup->getConnection()->endSetup();
    }
}
