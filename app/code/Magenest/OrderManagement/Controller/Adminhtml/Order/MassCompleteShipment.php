<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Magenest\OrderManagement\Model\Order;

/**
 * Class MassCompleteShipment
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
class MassCompleteShipment extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = "Magenest_OrderManagement::warehouse_complete_shipment";

    /**
     * {@inheritdoc}
     */
    public function massAction($collection)
    {
        $counter = 0;
        foreach ($collection as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            if ($this->_validateStatus($order->getStatus())) {
                $order->setStatus(Order::ORDER_COMPLETE_SHIPMENT);
                $this->_orderRepository->save($order);

                $counter++;
                $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                    'order' => $order,
                    'comment' => __("Warehouse confirm shipment was done.")
                ]);

                $this->_omOrder->sendCompleteShipmentEmail($order);
            }
        }

        return $counter;
    }
}