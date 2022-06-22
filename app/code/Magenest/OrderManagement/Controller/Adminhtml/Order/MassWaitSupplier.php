<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Magenest\OrderManagement\Model\Order;

/**
 * Class MassWaitSupplier
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
class MassWaitSupplier extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Magenest_OrderManagement::customer_service_wfs';

    /**
     * {@inheritdoc}
     */
    public function massAction($collection)
    {
        $counter = 0;
        foreach ($collection as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            if ($this->_validateStatus($order->getState())) {
                $order->setStatus(Order::WAIT_SUPPLIER_CODE);
                $this->_orderRepository->save($order);

                $counter++;
                $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                    'order' => $order,
                    'comment' => __("Customer service confirm that stock is not available, wait for supplier.")
                ]);
            }
        }

        return $counter;
    }
}
