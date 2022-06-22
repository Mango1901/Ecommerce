<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 *  Magenest_${PROJECT_NAME} extension
 *  NOTICE OF LICENSE
 *
 * @category Magenest
 *  @package Magenest_${PROJECT_NAME}
 *
 */

namespace Magenest\OrderManagement\Setup\Patch\Data;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order;

class UpdateStatusOrderMissing implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    const LAST_MIGRATION_ORDER_ID = 17569;

    private $orderResource;

    private $gridPool;

    private $orderCollection;

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * UpdateCompletedOrder constructor.
     *
     * @param Order\CollectionFactory $collectionFactory
     * @param \Magento\Sales\Model\ResourceModel\GridPool $gridPool
     * @param Order $orderResource
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Magento\Sales\Model\ResourceModel\GridPool $gridPool,
        \Magento\Sales\Model\ResourceModel\Order $orderResource,
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->orderCollection = $collectionFactory;
        $this->gridPool = $gridPool;
        $this->orderResource = $orderResource;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $orderCollection = $this->orderCollection->create()
            ->addFieldToFilter('entity_id', ['lteq' => self::LAST_MIGRATION_ORDER_ID])
            ->addFieldToFilter('status', 'shipping')
            ->addFieldToFilter('state', 'shipping')
            ->setPageSize(500);

        $curPage = 1;
        while (true) {
            $orderCollection->setCurPage($curPage);
            $this->orderResource->beginTransaction();
            try {
                foreach ($orderCollection as $order) {
                    $this->updateOrderState($order);
                }
                $this->orderResource->commit();
            } catch (\Exception $e) {
                $this->orderResource->rollBack();
            }
            if (count($orderCollection->getItems()) <= 0) {
                break;
            }
            $curPage++;
            $orderCollection->clear();
            $orderCollection->resetData();
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    private function updateOrderState($order)
    {
        $connection = $this->orderResource->getConnection();
        $saleOrder = $connection->getTableName('sales_order');
        $connection->update(
            $saleOrder, [
            OrderInterface::STATUS => \Magenest\OrderManagement\Model\Order::ORDER_COMPLETE_SHIPMENT
        ], $connection->quoteInto('entity_id = ?', $order->getId()));
        // Refresh sales order grid table
        $this->gridPool->refreshByOrderId($order->getId());
    }
}