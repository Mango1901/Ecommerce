<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Magenest\OrderManagement\Model\Order;

/**
 * Class CompleteShipment
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
class CompleteShipment extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = "Magenest_OrderManagement::warehouse_complete_shipment";

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                if ($order->getStatus() != 'complete') {
                    return $this->resultRedirectFactory->create()->setPath('sales/*/');
                }

                $order->setStatus(Order::ORDER_COMPLETE_SHIPMENT);
                $this->orderRepository->save($order);

                $this->messageManager->addSuccessMessage(__('You have confirmed that shipment is completed.'));
                $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                    'order' => $order,
                    'comment' => __("Warehouse confirm shipment was done.")
                ]);

                $this->_omOrder->sendCompleteShipmentEmail($order);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $this->resultRedirectFactory->create()->setPath('sales/order/view', ['order_id' => $order->getEntityId()]);
        }

        return $this->resultRedirectFactory->create()->setPath('sales/*/');
    }
}