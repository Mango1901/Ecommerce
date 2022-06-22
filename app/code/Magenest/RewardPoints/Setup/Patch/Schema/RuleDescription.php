<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 02/12/2020 16:08
 */

namespace Magenest\RewardPoints\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\PatchInterface;

class RuleDescription implements \Magento\Framework\Setup\Patch\SchemaPatchInterface
{
    const REWARDPOINTS_RULE_TABLE_NAME = 'magenest_rewardpoints_rule';
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
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        if ($this->moduleDataSetup->tableExists(self::REWARDPOINTS_RULE_TABLE_NAME)) {

            $this->moduleDataSetup->getConnection()->addColumn(
                $this->moduleDataSetup->getTable(self::REWARDPOINTS_RULE_TABLE_NAME),
                'description',
                [
                    'type' => Table::TYPE_TEXT,
                    'comment' => 'Rule description'
                ]
            );

        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }
}