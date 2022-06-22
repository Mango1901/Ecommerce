<?php
namespace Magenest\RealShippingMethod\Model;

use Magenest\RealShippingMethod\Setup\Patch\Data\UpdateOrderStatus;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Handler\State;

class OrderState extends State
{
    /**
     * Check order status and adjust the status before save
     *
     * @param Order $order
     *
     * @return $this
     */
    public function check(Order $order)
    {
        $currentState = $order->getState();
        if ($currentState == Order::STATE_NEW && $order->getIsInProcess()) {
            $order->setState(Order::STATE_PROCESSING)
                ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING));
            $currentState = Order::STATE_PROCESSING;
        }

        if (!$order->isCanceled() && !$order->canUnhold() && !$order->canInvoice()) {
            if (in_array($currentState, [Order::STATE_PROCESSING])
                && !$order->canCreditmemo()
                && !$order->canShip()
                && $order->getIsNotVirtual()
            ) {
                $order->setState(Order::STATE_CLOSED)
                    ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_CLOSED));
            } elseif ($currentState === Order::STATE_PROCESSING && !$order->canShip()
                && !in_array(
                    $order->getStatus(),
                    [
                        UpdateOrderStatus::PROCESSING_SHIPMENT_STATUS,
                        UpdateOrderStatus::ERP_SYNCED_STATUS,
                        UpdateOrderStatus::ERP_SYNCED_FAILED_STATUS
                    ]
                )) {
//                $order->setState(Order::STATE_COMPLETE)
//                    ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_COMPLETE));
            }
        }
        return $this;
    }
}
