<?php

namespace Magenest\MasOffer\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class SalesOrderSaveAfter implements ObserverInterface
{
    protected $moHelper;

    protected $registry;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magenest\MasOffer\Helper\MasOffer $moHelper
    ) {
        $this->moHelper = $moHelper;
        $this->registry = $registry;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($this->registry->registry('mas_offer_complete_order') == null
            && $order->getState() == Order::STATE_COMPLETE
            && $order->getIsAccessTrade() == 1) {
            $this->registry->register('mas_offer_complete_order', 1);
            $this->moHelper->postBackCompleteOrder($order);
            $this->registry->unregister('mas_offer_complete_order');
        }
        if ($this->registry->registry('mas_offer_cancel_order') == null
            && $order->getState() == Order::STATE_CANCELED
            && $order->getIsAccessTrade() == 1) {
            $this->registry->register('mas_offer_cancel_order', 1);
            $this->moHelper->postBackCancelOrder($order);
            $this->registry->unregister('mas_offer_cancel_order');
        }
    }
}
