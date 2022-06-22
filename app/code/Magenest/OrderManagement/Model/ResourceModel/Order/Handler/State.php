<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\OrderManagement\Model\ResourceModel\Order\Handler;

use Magento\Sales\Model\Order;
use Magenest\OrderManagement\Model\Order as OmOrder;

/**
 * Class State
 * @package Magenest\OrderManagement\Model\ResourceModel\Order\Handler
 */
class State extends \Magento\Sales\Model\ResourceModel\Order\Handler\State
{
    /**
     * @var OmOrder
     */
    protected $_omOrder;

    /**
     * Constructor.
     *
     * @param OmOrder $omOrder
     */
    function __construct(OmOrder $omOrder)
    {
        $this->_omOrder = $omOrder;
    }

    /**
     * {@inheritdoc}
     */
    public function check(Order $order)
    {
//        if(isset($GLOBALS['magenest_pos_is_processing']) && $GLOBALS['magenest_pos_is_processing']){
//            return parent::check($order);
//        }
        $currentState = $order->getState();
        if ($currentState == Order::STATE_NEW && $order->getIsInProcess()) {
            $order->setState(Order::STATE_PROCESSING)
                ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING));
            $currentState = Order::STATE_PROCESSING;
        }

        if (!$order->isCanceled() && !$order->canUnhold() && (!$order->canInvoice() || $this->_omOrder->waitOnlineInvoice($order))) {
            if (in_array($currentState, [Order::STATE_PROCESSING, Order::STATE_COMPLETE])
                && !$order->canCreditmemo()
                && !$order->canShip()
            ) {
                if (!$order->getIsVirtual() && $order->getOrigData('status') != OmOrder::SUPPLIER_REJECTED_CODE) {
                    $status = OmOrder::NEED_WAREHOUSE_CONFIRM_CODE;
                } else {
                    $status = $order->getConfig()->getStateDefaultStatus(Order::STATE_CLOSED);
                }

                $order->setState(Order::STATE_CLOSED)
                    ->setStatus($status);
            } elseif ($currentState === Order::STATE_PROCESSING && !$order->canShip()) {
                $order->setState(Order::STATE_COMPLETE)
                    ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_COMPLETE));
            }
        }

        return $this;
    }
}
