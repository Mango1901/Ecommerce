<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Psr\Log\LoggerInterface;
use Magento\OfflinePayments\Model\Cashondelivery;
use Magento\Sales\Controller\Adminhtml\Order as Action;
use Magenest\OrderManagement\Model\Order;

/**
 * Class Confirm
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
class Confirm extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Magenest_OrderManagement::customer_service_confirm';

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                if ($this->_omOrder->canConfirm($order)) {
                    $user      = $this->_adminSession->getUser();
                    $warehouse = $this->getRequest()->getParam('warehouse', null);
                    $orderCreator =  $this->getRequest()->getParam('order_creator', null);
                    $order->setState('processing')
                        ->setStatus(Order::CONFIRMED_WAREHOUSE_SALES_CODE)
                        ->setOrderCreator($orderCreator)
                        ->setConfirmedPersonName($user->getId())
                        ->setWarehouse($warehouse)
                        ->setConfirmedAt((new \DateTime())->format('Y-m-d H:i:s'));
                    $this->orderRepository->save($order);

                    $this->messageManager->addSuccessMessage(__('You have confirmed this order can be fulfilled.'));
                    $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                        'order' => $order,
                        'comment' => __("Customer service confirm warehouse and salesman.")
                    ]);

                    $this->_omOrder->sendEmailToCustomerWhenOrderConfirmed($order);
//                    if ($this->_omOrder->isCODOrder($order)) { // Need to disable because customer requirement change
                    $this->_omOrder->sendAccountantNotificationEmail($order);
//                    }
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $this->resultRedirectFactory->create()->setPath('sales/order/view', ['order_id' => $order->getEntityId()]);
        }

        return $this->resultRedirectFactory->create()->setPath('sales/*/');
    }
}
