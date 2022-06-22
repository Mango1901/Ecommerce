<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 *  Magenest_Kangaroo extension
 *  NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 *
 */

namespace Magenest\CustomCatalog\Setup\Patch\Schema;

use Magenest\CustomCatalog\Setup\Patch\PriceBillionSchema;

class UpdateRewardPointLimit extends PriceBillionSchema
{
    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->setup->startSetup();
        $this->updateTableColumns(
            'quote', [
            'reward_point',
            'reward_amount',
            'base_reward_amount',
            'base_grand_total_without_reward',
            'grand_total_without_reward'
        ]);
        $this->updateTableColumns(
            'sales_order', [
            'reward_point',
            'reward_amount',
            'base_reward_amount',
            'base_reward_invoiced',
            'reward_invoiced',
            'base_reward_refunded',
            'reward_refunded',
            'reward_tier',
            'bs_customer_mgn_rwp_total_refunded',
            'customer_mgn_rwp_total_refunded',
        ]);
        $this->setup->endSetup();
    }
}
