<?php

namespace Magenest\FastErp\Plugin\Magento\Quote\Model\Quote\Item;

use Magento\Quote\Model\Quote\Item;
use Magenest\OrderManagement\Model\Order;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Sales\Api\Data\OrderItemInterface;

class ToOrderItem
{
    /**
     * Apply dimension per every item in order if available
     *
     * @param Item\ToOrderItem $subject
     * @param OrderItemInterface $orderItem
     * @param AbstractItem $item
     * @param array $additional
     * @return OrderItemInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterConvert(
        Item\ToOrderItem $subject,
        OrderItemInterface $orderItem,
        AbstractItem $item,
        $additional = []
    ) {
        $orderItem->setWidth($item->getWidth() ?? null);
        $orderItem->setHeight($item->getHeight() ?? null);
        $orderItem->setLength($item->getLength() ?? null);

        return $orderItem;
    }
}
