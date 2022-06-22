<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Magenest\OrderManagement\Model\Order;
use Magento\Sales\Model\Order as SalesOrder;

/**
 * Class MassReturned
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
class MassReturned extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Magenest_OrderManagement::warehouse_received_returned_goods';

    /**
     * {@inheritdoc}
     */
    public function massAction($collection)
    {
        $counter = 0;
        foreach ($collection as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            if ($this->_validateStatus($order->getStatus())) {
                $isConfirmed = filter_var($this->getRequest()->getParam('is_confirmed'), FILTER_VALIDATE_BOOLEAN);
                if ($isConfirmed) {
                    $message = __("Warehouse confirm that returned goods is fully received without damage.");
                    $status = $order->getConfig()->getStateDefaultStatus(SalesOrder::STATE_CLOSED);
                } else {
                    $message = __("Warehouse confirm that returned goods is received. But need reimbursed.");
                    $status = Order::NEED_CONFIRM_REIMBURSEMENT_CODE;
                }

                $order->setState(SalesOrder::STATE_CLOSED)->setStatus($status);
                $this->_orderRepository->save($order);

                $counter++;
                $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                    'order' => $order,
                    'comment' => $message
                ]);
            }
        }

        return $counter;
    }
}