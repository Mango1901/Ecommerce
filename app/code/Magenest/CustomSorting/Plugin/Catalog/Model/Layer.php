<?php

namespace Magenest\CustomSorting\Plugin\Catalog\Model;

use Magento\Catalog\Block\Product\ProductList\Toolbar as MagentoToolbar;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Data\Collection;

/**
 * Class Layer
 * @package Magenest\CustomSorting\Plugin\Catalog\Model
 */
class Layer
{
    /**
     * @param MagentoToolbar $subject
     * @param Collection $collection
     * @return array
     */
    public function beforeSetCollection(MagentoToolbar $subject, Collection $collection): array
    {
        $currentOrder = $subject->getCurrentOrder();

        switch ($currentOrder) {
            case 'name_desc':
                $collection->setOrder('name', SortOrder::SORT_DESC);
                break;
            case 'price_asc':
                $collection->setOrder('price', SortOrder::SORT_ASC);
                break;
            case 'price_desc':
                $collection->setOrder('price', SortOrder::SORT_DESC);
                break;
            case 'create_at_desc':
                $collection->setOrder(
                    'created_at',
                    SortOrder::SORT_DESC
                );
                break;
            case 'magenest_rating':
                $subject->setDefaultDirection('desc');
                $collection->setOrder(
                    'magenest_rating',
                    SortOrder::SORT_DESC
                );
                break;
        }

        return [$collection];
    }
}
