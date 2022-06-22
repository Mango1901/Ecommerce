<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kangaroo extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\CustomCatalog\Plugin\Magento\Catalog\Block\Product\ProductList;

class Toolbar
{
    public function afterGetCurrentDirection(\Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar, $result)
    {
        if ($toolbar->getCurrentOrder() == "name") {
            $toolbar->setData('_current_grid_direction', 'asc');
            $result = 'asc';
        }

        return $result;
    }
}
