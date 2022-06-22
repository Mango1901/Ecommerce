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

class UpdateProductPriceLimit extends PriceBillionSchema
{
    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->setup->startSetup();
        $this->updateTableColumns('catalog_product_entity_decimal', ['value']);
        $this->updateTableColumns('quote_item', ['price', 'base_price'], false);
        $this->updateTableColumns('quote_item', ['custom_price', 'original_custom_price']);
        $this->updateTableColumns('sales_order_item', ['base_cost', 'original_price', 'base_original_price']);
        $this->updateTableColumns('sales_order_item', ['price', 'base_price'], false);
        $this->updateTableColumns('catalog_product_bundle_price_index', ['min_price', 'max_price'], false);
        $this->setup->endSetup();
    }
}
