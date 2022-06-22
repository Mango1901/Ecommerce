<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 * Created by PhpStorm.
 * User: crist
 * Date: 19/11/2021
 * Time: 09:54
 */

namespace Magenest\FastErp\Observer;

use Magenest\FastErp\Model\UpdateOrder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Shipment;

class SyncErpOrder implements ObserverInterface
{
    protected $updateOrder;

    public function __construct(
        UpdateOrder $updateOrder
    ) {
        $this->updateOrder = $updateOrder;
    }

    public function execute(Observer $observer)
    {
        /** @var Shipment $shipment */
        $shipment = $observer->getShipment();
        $order    = $shipment->getOrder();
        $this->updateOrder->execute($order);
    }
}
