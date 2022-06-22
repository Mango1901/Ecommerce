<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Magento\Sales\Controller\Adminhtml\Order as Action;
use Magenest\OrderManagement\Model\Order;

/**
 * Class Reimbursed
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
class Reimbursed extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Magenest_OrderManagement::accounting_confirm_reimbursed';

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                if ($order->getStatus() != Order::NEED_CONFIRM_REIMBURSEMENT_CODE) {
                    return $this->resultRedirectFactory->create()->setPath('sales/*/');
                }

                $order->setState('closed')->setStatus(Order::REIMBURSED_CODE);
                $this->orderRepository->save($order);

                $this->messageManager->addSuccessMessage(__('You have confirmed order is reimbursed.'));
                $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                    'order' => $order,
                    'comment' => __("Account confirm that order is reimbursed.")
                ]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $this->resultRedirectFactory->create()->setPath('sales/order/view', ['order_id' => $order->getEntityId()]);
        }

        return $this->resultRedirectFactory->create()->setPath('sales/*/');
    }
}
