<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kangaroo extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\CustomCatalog\Setup\Patch\Schema;

use Magenest\CustomCatalog\Setup\Patch\PriceBillionSchema;

class UpdateSalesOrderItemLimit extends PriceBillionSchema
{

    public function apply()
    {
        $this->updateTableColumns(
            'sales_order_item', [
            'price',
            'base_price'
        ], false);
        $this->updateTableColumns(
            'sales_order_item', [
            'original_price',
            'base_original_price',
            'weee_tax_applied_row_amount',
            'base_weee_tax_disposition',
            'tax_canceled',
            'base_weee_tax_applied_amount',
            'weee_tax_applied_amount',
            'base_weee_tax_row_disposition',
            'base_weee_tax_applied_row_amnt',
            'weee_tax_row_disposition',
            'weee_tax_disposition'
        ]);
        $this->updateTableColumns('sales_order_grid', ['refunded_to_mgn_rwp']);
        $this->updateTableColumns(
            'sales_invoice', [
            'reward_point',
            'reward_amount',
            'base_reward_amount',
            'reward_tier',
        ]);
        $this->updateTableColumns(
            'sales_creditmemo', [
            'reward_point',
            'reward_amount',
            'base_reward_amount',
            'reward_tier',
        ]);
    }
}
