<?php
/**
 * @copyright Copyright (c) magenest.com, Inc. (https://www.magenest.com)
 */

namespace Magenest\OrderManagement\Observer;

use Exception;
use Magenest\OrderManagement\Model\Order as OmOrder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\OrderFactory;
use Psr\Log\LoggerInterface;

class PosOrderSaveAfter implements ObserverInterface
{
    /**
     * @var OmOrder
     */
    protected $_omOrder;

    protected $logger;

    protected $processed = false;

    protected $orderFactory;

    /**
     * Constructor.
     *
     * @param OmOrder $omOrder
     * @param LoggerInterface $logger
     */
    public function __construct(
        OmOrder $omOrder,
        LoggerInterface $logger,
        OrderFactory $orderFactory
    ) {
        $this->_omOrder     = $omOrder;
        $this->logger       = $logger;
        $this->orderFactory = $orderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        if ($this->processed) {
            return;
        }
        try {
            $postOrder = $observer->getDataObject();
            $oderId    = $postOrder->getOrderId();
            $order     = $this->orderFactory->create()->load($oderId);
            if ($oderId && $order->getPayment()->getMethod() == 'poscash' && $order->getStatus() == 'complete_shipment' && !$order->getSendPosEmail()) {
                $this->_omOrder->sendAccountantNotificationEmail($order, true);
                $order->setSendPosEmail(1);
                $order->save();
            }
            $this->processed = true;
        } catch (Exception $e) {
            //
            $this->logger->critical($e->getMessage());
        }
    }
}
