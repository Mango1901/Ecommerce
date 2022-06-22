<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ProductLabel
 */

namespace Magenest\ProductLabel\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class LabelType
 * @package Magenest\ProductLabel\Model\Config\Source
 */
class ProductState implements ArrayInterface
{

    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => 'All',
                'value' => \Magenest\ProductLabel\Api\Data\ConstantInterface::PRODUCT_LABEL_NORMAL_TYPE
            ],
            [
                'label' => 'New',
                'value' => \Magenest\ProductLabel\Api\Data\ConstantInterface::PRODUCT_LABEL_NEW_TYPE
            ],
            [
                'label' => 'On Sale',
                'value' => \Magenest\ProductLabel\Api\Data\ConstantInterface::PRODUCT_LABEL_SALE_TYPE
            ],
            [
                'label' => 'Best Seller',
                'value' => \Magenest\ProductLabel\Api\Data\ConstantInterface::PRODUCT_LABEL_BEST_SELLER
            ],
        ];
    }
}
