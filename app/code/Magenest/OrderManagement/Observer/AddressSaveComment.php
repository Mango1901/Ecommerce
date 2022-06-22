<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 16/11/2021
 * Time: 09:20
 */

namespace Magenest\OrderManagement\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class AddressSaveComment
 */
class AddressSaveComment implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    protected $orderRepository;

    /**
     * AddressSaveComment constructor.
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->_eventManager = $eventManager;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $orderId = $observer->getOrderId();
        try {
            $order = $this->orderRepository->get($orderId);
            $this->_eventManager->dispatch(
                "order_management_action_dispatch_save_comment_history",
                [
                    'order' => $order,
                    'comment' => __("Order Address has been edited.")
                ]
            );
        } catch (\Exception $exception) {
        }
    }
}
