<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Magento\Sales\Controller\Adminhtml\Order as Action;
use Magenest\OrderManagement\Model\Order;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Sales\Model\Order as SalesOrder;

/**
 * Class Returned
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
class Returned extends Action implements HttpPostActionInterface
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Magenest_OrderManagement::warehouse_received_returned_goods';

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setPath('sales/*/');
        }

        $order = $this->_initOrder();
        if ($order) {
            try {
                if ($order->getStatus() != Order::NEED_WAREHOUSE_CONFIRM_CODE) {
                    return $this->resultRedirectFactory->create()->setPath('sales/*/');
                }

                $isConfirmed = filter_var($this->getRequest()->getParam('is_confirmed'), FILTER_VALIDATE_BOOLEAN);
                if ($isConfirmed) {
                    $message = __("Warehouse confirm that returned goods is fully received without damage.");
                    $status = $order->getConfig()->getStateDefaultStatus(SalesOrder::STATE_CLOSED);
                } else {
                    $message = __("Warehouse confirm that returned goods is received. But need reimbursed.");
                    $status = Order::NEED_CONFIRM_REIMBURSEMENT_CODE;
                }

                $order->setState(SalesOrder::STATE_CLOSED)->setStatus($status);
                $this->orderRepository->save($order);

                $this->messageManager->addSuccessMessage(__('You have confirmed that goods is received.'));
                $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                    'order' => $order,
                    'comment' => $message
                ]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $this->resultRedirectFactory->create()->setPath('sales/order/view', ['order_id' => $order->getEntityId()]);
        }

        return $this->resultRedirectFactory->create()->setPath('sales/*/');
    }
}
