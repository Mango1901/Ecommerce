<?php

namespace Magenest\CustomSorting\Plugin\Catalog\Model;

use Magento\Catalog\Model\Config as MagentoConfig;

class Config
{
    /**
     * @param MagentoConfig $subject
     * @param array $result
     * @return array
     */
    public function afterGetAttributeUsedForSortByArray(MagentoConfig $subject, array $result): array
    {
        return [
            'name_desc' => __("Product Name"),
            'create_at_desc' => __("New Product"),
            'price_asc' => __("Price (Low To High)"),
            'price_desc' => __("Price (High To Low)"),
//            'magenest_rating' => __("Average Rating"),
        ];
    }
}
