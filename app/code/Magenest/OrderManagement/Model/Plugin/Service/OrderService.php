<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\OrderManagement\Model\Plugin\Service;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class OrderService
 */
class OrderService
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var ManagerInterface
     */
    protected $_eventManager;

    /**
     * Constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param ManagerInterface $eventManager
     */
    function __construct(
        OrderRepositoryInterface $orderRepository,
        ManagerInterface $eventManager
    )
    {
        $this->_orderRepository = $orderRepository;
        $this->_eventManager = $eventManager;
    }

    /**
     * After un hold
     *
     * @param \Magento\Sales\Model\Service\OrderService $subject
     * @param $result
     * @param $id
     * @return
     */
    public function afterUnHold(
        \Magento\Sales\Model\Service\OrderService $subject,
        $result,
        $id
    )
    {
        $order = $this->_orderRepository->get($id);
        $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
            'order' => $order,
            'comment' => __('Accountant released the order from holding status.')
        ]);

        return $result;
    }

    /**
     * After un hold
     *
     * @param \Magento\Sales\Model\Service\OrderService $subject
     * @param $result
     * @param $id
     * @return
     */
    public function afterHold(
        \Magento\Sales\Model\Service\OrderService $subject,
        $result,
        $id
    )
    {
        $order = $this->_orderRepository->get($id);
        $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
            'order' => $order,
            'comment' => __('Accountant put the order on hold.')
        ]);

        return $result;
    }
}
