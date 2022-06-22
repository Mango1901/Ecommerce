<?php

namespace Magenest\CustomSorting\Plugin\Config\Source;

use Magento\Catalog\Model\Config\Source\ListSort as MagentoListSort;

class ListSort
{
    /**
     * @param MagentoListSort $subject
     * @param array $result
     * @return array
     */
    public function afterToOptionArray(MagentoListSort $subject, array $result): array
    {
        return [
            [
                'label' => __("Product Name"),
                'value' => 'name_desc'
            ],
            [
                'label' => __("Price (Low To High)"),
                'value' => 'price_asc'
            ],
            [
                'label' => __("Price (High To Low)"),
                'value' => 'price_desc'
            ],
            [
                'label' => __("Newest Product"),
                'value' => 'update_at_desc'
            ],
//            [
//                'label' => __("Average Rating"),
//                'value' => 'magenest_rating'
//            ]
        ];
    }
}
