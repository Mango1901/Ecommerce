<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Magenest\OrderManagement\Model\Order;
use Magento\OfflinePayments\Model\Cashondelivery;

/**
 * Class MassConfirm
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
class MassConfirm extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Magenest_OrderManagement::customer_service_confirm';

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\MailException
     */
    public function massAction($collection)
    {
        $counter = 0;
        foreach ($collection as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            if ($this->_omOrder->canConfirm($order)) {
                $user = $this->_adminSession->getUser();
                $order->setState('processing')
                    ->setStatus(Order::CONFIRMED_WAREHOUSE_SALES_CODE)
                    ->setConfirmedPersonName("{$user->getFirstName()} {$user->getLastName()}")
                    ->setConfirmedAt((new \DateTime())->format('Y-m-d H:i:s'));
                $this->_orderRepository->save($order);

                $counter++;
                $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                    'order' => $order,
                    'comment' => __("Customer service confirm warehouse and salesman.")
                ]);

                $this->_omOrder->sendEmailToCustomerWhenOrderConfirmed($order);
//                if ($order->getPayment() && $order->getPayment()->getMethod() == Cashondelivery::PAYMENT_METHOD_CASHONDELIVERY_CODE) { // Need to disable because customer requirement change
                    $this->_omOrder->sendAccountantNotificationEmail($order);
//                }
            }
        }

        return $counter;
    }
}