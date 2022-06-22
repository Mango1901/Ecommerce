<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Psr\Log\LoggerInterface;
use Magenest\OrderManagement\Model\Order as OmOrder;

/**
 * Class ReceivedGoods
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
class ReceivedGoods extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Magenest_OrderManagement::warehouse_received_goods';

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                if ($order->getStatus() != OmOrder::SUPPLIER_CONFIRMED_CODE) {
                    return $this->resultRedirectFactory->create()->setPath('sales/*/');
                }

                $order->setState('new')->setStatus(OmOrder::RECEIVED_GOODS_CODE)->setReceivedGoodsAt((new \DateTime())->format('Y-m-d H:i:s'));
                $this->orderRepository->save($order);

                $this->messageManager->addSuccessMessage(__('You have confirmed goods is delivered.'));
                $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                    'order' => $order,
                    'comment' => __("Warehouse confirm that goods is delivered.")
                ]);


                $this->_omOrder->sendCustomerServiceReceivedGoodsNotificationEmail($order);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $this->resultRedirectFactory->create()->setPath('sales/order/view', ['order_id' => $order->getEntityId()]);
        }

        return $this->resultRedirectFactory->create()->setPath('sales/*/');
    }
}
