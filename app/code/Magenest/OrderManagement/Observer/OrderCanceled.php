<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magenest\OrderManagement\Model\Order as OmOrder;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class OrderCanceled
 * @package Magenest\OrderManagement\Observer
 */
class OrderCanceled implements ObserverInterface
{
    /**
     * @var OmOrder
     */
    protected $_omOrder;

    /**
     * @var ManagerInterface
     */
    protected $_eventManager;

    protected $logger;

    /**
     * Constructor.
     *
     * @param OmOrder $omOrder
     * @param ManagerInterface $eventManager
     * @param \Psr\Log\LoggerInterface $logger
     */
    function __construct(
        OmOrder $omOrder,
        ManagerInterface $eventManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->_omOrder = $omOrder;
        $this->_eventManager = $eventManager;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        /** Send email to customer */
        try {
            $this->_omOrder->sendCanceledEmail($order);
            $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                'order' => $order,
                'comment' => __('Customer service was canceled this order.')
            ]);
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }

        return $this;
    }
}
