<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * User: leo
 * Date: 14/06/2022
 * Time: 11:30
 */
declare(strict_types=1);

namespace Magenest\NotificationBox\Api;

/**
 * Class IsProductHavePromotionInterface
 */
interface IsProductHavePromotionInterface
{
    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function execute($product);
}
