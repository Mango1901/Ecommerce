<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Magenest\OrderManagement\Model\Order;

/**
 * Class MassReimbursed
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
class MassReimbursed extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = "Magenest_OrderManagement::accounting_confirm_reimbursed";

    /**
     * {@inheritdoc}
     */
    public function massAction($collection)
    {
        $counter = 0;
        foreach ($collection as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            if ($this->_validateStatus($order->getStatus())) {
                $order->setState('closed')->setStatus(Order::REIMBURSED_CODE);
                $this->_orderRepository->save($order);

                $counter++;
                $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                    'order' => $order,
                    'comment' => __("Account confirm that order is reimbursed.")
                ]);
            }
        }

        return $counter;
    }
}