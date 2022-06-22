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

namespace Magenest\OrderManagement\Plugin\Magento\Quote\Model\Quote\Item;

use Magento\Quote\Model\Quote\Item;
use Magenest\OrderManagement\Model\Order;

class ToOrderItem
{
    public function afterConvert(\Magento\Quote\Model\Quote\Item\ToOrderItem $orderItem, $result, $item, $data = [])
    {
        if ($item instanceof Item && (int) $item->getData(Order::ORDER_ITEM_IS_BACKORDER) > 0) {
            $result->setData(Order::ORDER_ITEM_IS_BACKORDER, 1);
        }

        return $result;
    }
}
