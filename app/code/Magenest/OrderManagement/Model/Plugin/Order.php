<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Model\Plugin;

/**
 * Class Order
 * @package Magenest\OrderManagement\Model\Plugin
 */
class Order
{
    /**
     * After hold
     *
     * @param \Magento\Sales\Model\Order $subject
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Model\Order
     */
    public function afterHold(\Magento\Sales\Model\Order $subject, $order)
    {
        if ($order->getHoldBeforeStatus() != \Magenest\OrderManagement\Model\Order::CONFIRMED_WAREHOUSE_SALES_CODE) {
            return $order;
        }

        $order->setHoldUnpaidOrderAt((new \DateTime())->format('Y-m-d H:i:s'));

        return $order;
    }
}