<?php

namespace Magenest\MasOffer\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CheckoutOnePageSuccess implements ObserverInterface
{
    /**
     * @var \Magenest\MasOffer\Helper\MasOffer
     */
    protected $moHelper;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * CheckoutOnePageSuccess constructor.
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magenest\MasOffer\Helper\MasOffer $moHelper
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magenest\MasOffer\Helper\MasOffer $moHelper
    ) {
        $this->moHelper        = $moHelper;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->moHelper->isEnabled()) {
            $orderIds = $observer->getData('order_ids');
            if (!isset($orderIds[0])) {
                return;
            }
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->orderRepository->get($orderIds[0]);
            $this->moHelper->postBack($order);
        }
    }
}
