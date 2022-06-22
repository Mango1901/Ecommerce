<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 10/12/2021
 * Time: 13:53
 */

namespace Magenest\OrderCancel\Block\Order;

use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order\Config;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;

class History extends \Magento\Sales\Block\Order\History
{
    /**
     * @var CollectionFactoryInterface
     */
    private $orderCollectionFactory;

    const STATUSES = [
        'pending'             => [
            'label'  => 'Pending confirmation',
            'status' => ['pending', 'pending_paid', 'pending_payment'],
            'url'    => 'sales/order_history/pending'
        ],
        'processing_shipment' => [
            'label'  => 'Delivering',
            'status' => ['confirmed', 'erp_synced', 'erp_synced_failed', 'packed', 'processing_shipment'],
            'url'    => 'sales/order_history/delivering'
        ],
        'delivered'           => [
            'label'  => 'Delivered',
            'status' => ['complete'],
            'url'    => 'sales/order_history/delivered'
        ],
        'canceled'            => [
            'label'  => 'Cancelled',
            'status' => ['canceled'],
            'url'    => 'sales/order_history/canceled'
        ],
    ];

    /**
     * History constructor.
     *
     * @param Context $context
     * @param CollectionFactory $orderCollectionFactory
     * @param Session $customerSession
     * @param Config $orderConfig
     * @param CollectionFactoryInterface|null $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context                    $context,
        CollectionFactory          $orderCollectionFactory,
        Session                    $customerSession,
        Config                     $orderConfig,
        CollectionFactoryInterface $collectionFactory = null,
        array                      $data = []
    ) {
        $this->orderCollectionFactory = $collectionFactory ??
            ObjectManager::getInstance()->get(CollectionFactoryInterface::class);
        parent::__construct($context, $orderCollectionFactory, $customerSession, $orderConfig, $data);
    }

    /**
     * Get customer orders
     *
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrders()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->orders) {
            $this->orders = $this->getOrderCollectionFactory()->create($customerId)->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'status',
                ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
            )->setOrder(
                'created_at',
                'desc'
            );
            if ($status = $this->getData('state')) {
                $this->orders->addFieldToFilter('status', self::STATUSES[$status]['status']);
            }
        }
        return $this->orders;
    }

    public function getTotalCountByState($state)
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        $orders = $this->getOrderCollectionFactory()->create($customerId)->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'status',
            ['in' => self::STATUSES[$state]['status']]
        )->setOrder(
            'created_at',
            'desc'
        );

        return $orders->getTotalCount();
    }

    public function getStatuses()
    {
        return self::STATUSES;
    }

    /**
     * Provide order collection factory
     *
     * @return CollectionFactoryInterface
     * @deprecated 100.1.1
     */
    private function getOrderCollectionFactory()
    {
        return $this->orderCollectionFactory;
    }

    /**
     * Get message for no orders.
     *
     * @return \Magento\Framework\Phrase
     * @since 102.1.0
     */
    public function getEmptyOrdersMessage()
    {
        return __('There is no order in this state.');
    }
}
