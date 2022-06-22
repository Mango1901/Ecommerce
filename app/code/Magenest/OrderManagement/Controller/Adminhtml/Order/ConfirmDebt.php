<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Exception;

/**
 * Class WaitSupplier
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
class ConfirmDebt extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Magenest_OrderManagement::accounting_confirm_debt';

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                if ($this->_omOrder->canConfirmDebt($order)) {
                    $order->setData('is_confirm_debt', 1);
                    $this->orderRepository->save($order);
                    $this->messageManager->addSuccessMessage(__('You have confirmed the debt to this order'));
                    $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                        'order' => $order,
                        'comment' => __("Accountant confirmed the debt to this order ")
                    ]);
                }
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $this->resultRedirectFactory->create()->setPath('sales/order/view', ['order_id' => $order->getEntityId()]);
        }

        return $this->resultRedirectFactory->create()->setPath('sales/*/');
    }
}
