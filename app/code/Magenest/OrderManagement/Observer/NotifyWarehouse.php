<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Observer;

use Magento\Sales\Model\Order;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Magenest\OrderManagement\Model\Order as OmOrder;

/**
 * Class OrderCanceled
 * @package Magenest\OrderManagement\Observer
 */
class NotifyWarehouse implements ObserverInterface
{
    /**
     * @var OmOrder
     */
    protected $_omOrder;

    protected $logger;

    /**
     * Constructor.
     *
     * @param OmOrder $omOrder
     */
    public function __construct(
        OmOrder $omOrder,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_omOrder = $omOrder;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var Creditmemo $creditmemo */
        try{
            $creditmemo = $observer->getCreditmemo();
            $this->_omOrder->sendWarehouseReturnedOrderNotificationEmail($creditmemo->getOrder());
        }catch (\Exception $e){
            //
            $this->logger->critical($e->getMessage());
        }
    }
}