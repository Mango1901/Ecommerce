<?php
/**
 * @copyright Copyright (c) magenest.com, Inc. (https://www.magenest.com)
 */

namespace Magenest\OrderManagement\Observer;

use Exception;
use Magenest\OrderManagement\Model\Order as OmOrder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SuccessAction
 * @package Magenest\OrderManagement\Observer
 */
class SuccessAction implements ObserverInterface
{
    /**
     * @var OmOrder
     */
    protected $_omOrder;

    protected $logger;

    protected $processed = false;

    /**
     * Constructor.
     *
     * @param OmOrder $omOrder
     * @param LoggerInterface $logger
     */
    public function __construct(
        OmOrder $omOrder,
        LoggerInterface $logger
    ) {
        $this->_omOrder = $omOrder;
        $this->logger   = $logger;
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
            $order = $observer->getOrder();
            $address = $order->getShippingAddress();
            if (!$order->getData('province') && $address) {
                $order->setData('province', $order->getShippingAddress()->getCity());
                $order->save();

            }
            $this->_omOrder->sendCustomerServiceNotificationEmail($order);
            $this->processed = true;
        } catch (Exception $e) {
            //
            $this->logger->critical($e->getMessage());
        }
    }
}
