<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Magenest\OrderManagement\Model\Order;

/**
 * Class MassReceivedGoods
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
class MassReceivedGoods extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = "Magenest_OrderManagement::warehouse_received_goods";

    /**
     * {@inheritdoc}
     */
    public function massAction($collection)
    {
        $counter = 0;
        foreach ($collection as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            if ($this->_validateStatus($order->getStatus())) {
                $order->setState('new')->setStatus(Order::RECEIVED_GOODS_CODE)->setReceivedGoodsAt((new \DateTime())->format('Y-m-d H:i:s'));
                $this->_orderRepository->save($order);

                $counter++;
                $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                    'order' => $order,
                    'comment' => __("Warehouse confirm that goods is delivered.")
                ]);

                $this->_omOrder->sendSupplierReceivedGoodsEmail($order);
                $this->_omOrder->sendCustomerServiceReceivedGoodsNotificationEmail($order);
            }
        }

        return $counter;
    }
}