<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\Plugin;

use Magento\Framework\Indexer\StateInterface;
use Aheadworks\AdvancedReports\Model\Indexer\Statistics\Processor as StatisticsProcessor;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Sales\Model\ResourceModel\Order;

/**
 * Class OrderSave
 *
 * @package Aheadworks\AdvancedReports\Model\Plugin
 */
class OrderSave
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    private $order;

    /**
     * @var string
     */
    private $orderStatus;

    /**
     * @var StateInterface
     */
    private $indexerState;

    /**
     * @param StateInterface $indexerState
     */
    public function __construct(
        StateInterface $indexerState
    ) {
        $this->indexerState = $indexerState;
    }

    /**
     * Retrieve order
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeLoad($interceptor, $order, $value, $field = null) {
        $this->order = $order;
        return [$order, $value, $field];
    }

    /**
     * Retrieve status
     *
     * @return AbstractDb
     */
    public function afterLoad(AbstractDb $object, $value, $field = null) {
        $this->orderStatus = $this->order->getStatus();
        return $object;
    }

    /**
     * Retrieve order
     *
     * @param Order $interceptor
     * @param \Magento\Sales\Model\Order $order
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(
        Order $interceptor,
        \Magento\Sales\Model\Order $order
    ) {
        $this->order = $order;
    }

    /**
     * Check change order status
     *
     * @param Order $interceptor
     * @return Order
     */
    public function afterSave(
        Order $interceptor
    ) {
        $this->indexerState->loadByIndexer(StatisticsProcessor::INDEXER_ID);
        if ($this->orderStatus != $this->order->getStatus()
            && $this->indexerState->getStatus() == StateInterface::STATUS_VALID
        ) {
            $this->indexerState->setStatus(StateInterface::STATUS_INVALID);
            $this->indexerState->save();
        }
        return $interceptor;
    }
}
