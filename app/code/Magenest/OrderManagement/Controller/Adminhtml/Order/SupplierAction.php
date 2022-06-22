<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Psr\Log\LoggerInterface;
use Magenest\OrderManagement\Model\Order as OmOrder;
use Magento\Sales\Controller\Adminhtml\Order as Action;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

/**
 * Class SupplierAction
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
class SupplierAction extends AbstractAction implements HttpPostActionInterface
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Magenest_OrderManagement::supplier_action';

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
                if ($order->getStatus() != OmOrder::WAIT_SUPPLIER_CODE) {
                    return $this->resultRedirectFactory->create()->setPath('sales/*/');
                }

                $isConfirmed = filter_var($this->getRequest()->getParam('is_confirmed'), FILTER_VALIDATE_BOOLEAN);
                if ($isConfirmed) {
                    $comment = __("Supplier confirm that goods can be delivered.");
                    $status = OmOrder::SUPPLIER_CONFIRMED_CODE;
                    $action = 'confirmed';
                } else {
                    $comment = __("Supplier confirm that goods cannot be delivered.");
                    $status = OmOrder::SUPPLIER_REJECTED_CODE;
                    $action = 'rejected';
                }

                $order->setStatus($status);
                $this->orderRepository->save($order);

                $this->messageManager->addSuccessMessage(__('You have %1 deliver goods.', $action));
                $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                    'order' => $order,
                    'comment' => $comment
                ]);

                if (!$isConfirmed) {
                    $this->_omOrder->sendCustomerServiceSupplierRejectNotificationEmail($order);
                } else {
                    $this->_omOrder->sendWarehouseNotificationEmail($order);
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $this->resultRedirectFactory->create()->setPath('sales/order/view', ['order_id' => $order->getEntityId()]);
        }

        return $this->resultRedirectFactory->create()->setPath('sales/*/');
    }
}
