<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Magenest\OrderManagement\Model\Order;

/**
 * Class MassConfirmPaid
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
class MassConfirmPaid extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Magenest_OrderManagement::accounting_confirm';

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\MailException
     */
    public function massAction($collection)
    {
        $counter = 0;
        foreach ($collection as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            if ($this->_validateStatus($order->getStatus()) && $this->_omOrder->canConfirmPaid($order)) {
                if ($this->_omOrder->isCODOrder($order)) {
                    $status = Order::CONFIRMED_COD_CODE;
                    $tag = 'COD';
                } else {
                    $status = Order::CONFIRMED_PAID_CODE;
                    $tag = 'fully paid';
                }

                $order->setState('processing')->setStatus($status)->setConfirmPaidAt((new \DateTime())->format('Y-m-d H:i:s'));
                $this->_orderRepository->save($order);
                $this->_omOrder->sendWarehouseNeedPackagingNotificationEmail($order);
                $counter++;
                $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                    'order' => $order,
                    'comment' => __("Accountant confirm order is %1.", $tag)
                ]);
            }
        }

        return $counter;
    }
}