<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Catalog\Product\Type;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Interface OptionsInterface
 * @package Magenest\MobileApi\Model\Catalog\Product\Type
 */
interface OptionsInterface
{
    /**
     * Process options for product
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     */
    public function process(ProductInterface $product);
}