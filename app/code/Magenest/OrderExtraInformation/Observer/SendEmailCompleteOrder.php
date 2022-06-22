<?php

namespace Magenest\OrderExtraInformation\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

/**
 * Class SendEmailCompleteOrder
 */
class SendEmailCompleteOrder implements ObserverInterface
{
    /**
     * @var \Magenest\OrderManagement\Model\Order
     */
    private $orderManagement;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        \Magenest\OrderManagement\Model\Order $orderManagement,
        LoggerInterface $logger
    ) {
        $this->orderManagement = $orderManagement;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if (!$order->getId()) {
            //order not saved in the database
            return $this;
        }

        if ($order->getState() == Order::STATE_COMPLETE) {
            try {
                $this->orderManagement->sendCompleteShipmentEmail($order);
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
            }
        }

        return $this;
    }
}
