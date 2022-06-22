<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 21/12/2021
 * Time: 09:16
 */

namespace Magenest\FastErp\Model;

use Magento\Framework\App\ResourceConnection;

class GetAllocatedSourceCodeForOrder
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Get allocated sources by order ID
     *
     * @param int $orderId
     * @return array
     */
    public function execute(int $orderId): array
    {
        $sources = [];
        $salesConnection = $this->resourceConnection->getConnection('sales');
        $shipmentTableName = $this->resourceConnection->getTableName('sales_shipment', 'sales');
        /** Get shipment ids for order */
        $shipmentSelect = $salesConnection->select()
            ->from(
                ['sales_shipment' => $shipmentTableName],
                ['shipment_id' => 'sales_shipment.entity_id']
            )
            ->where('sales_shipment.order_id = ?', $orderId);
        $shipmentsIds = $salesConnection->fetchCol($shipmentSelect);

        /** Get sources for shipment ids */
        if (!empty($shipmentsIds)) {
            $connection = $this->resourceConnection->getConnection();
            $inventorySourceTableName = $this->resourceConnection->getTableName('inventory_source');
            $inventoryShipmentSourceTableName = $this->resourceConnection->getTableName('inventory_shipment_source');

            $select = $connection->select()
                ->from(
                    ['inventory_source' => $inventorySourceTableName],
                    ['source_name' => 'inventory_source.source_code']
                )
                ->joinInner(
                    ['shipment_source' => $inventoryShipmentSourceTableName],
                    'shipment_source.source_code = inventory_source.source_code',
                    []
                )
                ->group('inventory_source.source_code')
                ->where('shipment_source.shipment_id in (?)', $shipmentsIds);

            $sources = $connection->fetchCol($select);
        }

        return $sources;
    }
}
