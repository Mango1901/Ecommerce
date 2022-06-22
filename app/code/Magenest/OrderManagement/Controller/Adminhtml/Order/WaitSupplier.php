<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Magenest\OrderManagement\Model\Order;

/**
 * Class WaitSupplier
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
class WaitSupplier extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Magenest_OrderManagement::customer_service_wfs';

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                if ('new' == $order->getState()) {
                    $order->setStatus(Order::WAIT_SUPPLIER_CODE);
                    $this->orderRepository->save($order);

                    $this->messageManager->addSuccessMessage(__('You have confirmed the stock not availability, need supplier deliver goods.'));
                    $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                        'order' => $order,
                        'comment' => __("Customer service confirm that stock is not available, wait for supplier.")
                    ]);
                    $this->_omOrder->sendSupplierReceivedGoodsEmail($order);
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $this->resultRedirectFactory->create()->setPath('sales/order/view', ['order_id' => $order->getEntityId()]);
        }

        return $this->resultRedirectFactory->create()->setPath('sales/*/');
    }
}
