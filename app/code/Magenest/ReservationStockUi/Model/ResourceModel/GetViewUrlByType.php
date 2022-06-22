<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ReservationStockUi extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ReservationStockUi
 */

namespace Magenest\ReservationStockUi\Model\ResourceModel;

use Magento\Framework\UrlInterface;
use Magento\InventorySalesApi\Api\Data\SalesEventInterface;
use Magenest\ReservationStockUi\Api\Data\SalesEventMetadataInterface;

class GetViewUrlByType
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resources;

    private $_typeMaps = [
        SalesEventInterface::EVENT_ORDER_PLACED => 'order',
        SalesEventInterface::EVENT_ORDER_CANCELED => 'order',
        SalesEventInterface::EVENT_SHIPMENT_CREATED => 'shipment',
        SalesEventInterface::EVENT_CREDITMEMO_CREATED => 'creditmemo',
        SalesEventInterface::EVENT_INVOICE_CREATED => 'invoice',
    ];

    /**
     * GetViewUrlByType constructor.
     *
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\App\ResourceConnection $resources
     */
    public function __construct(
        UrlInterface $urlBuilder,
        \Magento\Framework\App\ResourceConnection $resources
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->resources = $resources;
    }

    public function execute($type, array $metadata)
    {
        if (!array_key_exists($type, $this->_typeMaps) || !isset($metadata[SalesEventMetadataInterface::OBJECT_INCREMENT_ID])) {
            return false;
        }
        $callable = "get{$this->_typeMaps[$type]}ViewUrl";

        return call_user_func_array([$this, $callable], [$metadata]);
    }

    public function getOrderViewUrl(array $metadata)
    {
        $incrementId = $metadata[SalesEventMetadataInterface::OBJECT_INCREMENT_ID];
        $connection = $this->getConnection();
        $select = $connection->select()->from($connection->getTableName('sales_order'), 'entity_id');
        $select->where('increment_id = ?', $incrementId);
        $orderId = $connection->fetchOne($select);
        $objectId = isset($metadata[SalesEventMetadataInterface::OBJECT_ID]) ? $metadata[SalesEventMetadataInterface::OBJECT_ID] : false;
        if ($orderId && ($orderId == $objectId || empty($objectId))) {
            return $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $orderId]);
        }

        return false;
    }

    private function getConnection()
    {
        return $this->resources->getConnection();
    }

    public function getShipmentViewUrl(array $metadata)
    {
        return $this->getOrderViewUrl($metadata);
    }

    public function getInvoiceViewUrl(array $metadata)
    {
        return $this->getOrderViewUrl($metadata);
    }

    public function getCreditmemoViewUrl(array $metadata)
    {
        return $this->getOrderViewUrl($metadata);
    }
}
