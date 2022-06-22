<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Magenest\OrderManagement\Model\Order;
use Magento\Sales\Model\Order as SalesOrder;

/**
 * Class MassSupplierAction
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
class MassSupplierAction extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Magenest_OrderManagement::supplier_action';

    /**
     * {@inheritdoc}
     */
    public function massAction($collection)
    {
        $counter = 0;
        $isConfirmed = filter_var($this->getRequest()->getParam('is_confirmed'), FILTER_VALIDATE_BOOLEAN);

        if ($isConfirmed) {
            $comment = __("Supplier confirm that goods can be delivered.");
            $status = Order::SUPPLIER_CONFIRMED_CODE;
        } else {
            $comment = __("Supplier confirm that goods cannot be delivered.");
            $status = Order::SUPPLIER_REJECTED_CODE;
        }

        foreach ($collection as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            if ($this->_validateStatus($order->getStatus())) {
                $order->setStatus($status);
                $this->_orderRepository->save($order);

                $counter++;
                $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                    'order' => $order,
                    'comment' => $comment
                ]);

                if (!$isConfirmed) {
                    $this->_omOrder->sendCustomerServiceSupplierRejectNotificationEmail($order);
                }
            }
        }

        return $counter;
    }
}