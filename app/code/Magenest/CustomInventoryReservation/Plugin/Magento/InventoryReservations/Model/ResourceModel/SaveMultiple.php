<?php
/**
 * Copyright © CustomInventoryReservation All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\CustomInventoryReservation\Plugin\Magento\InventoryReservations\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magenest\CustomInventoryReservation\Api\ReservationInterfaceV2;
use Magenest\CustomSource\Helper\Data;

class SaveMultiple
{
    /**
     * @var Data
     */
    private $dataHelper;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param Data $dataHelper
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Data $dataHelper,
        ResourceConnection $resourceConnection
    ) {
        $this->dataHelper = $dataHelper;
        $this->resourceConnection = $resourceConnection;
    }

    public function aroundExecute(
        \Magento\InventoryReservations\Model\ResourceModel\SaveMultiple $subject,
        \Closure $proceed,
        array $reservations
    ) {
        $areaCode = $this->dataHelper->getCurrentArea();
        if(isset($areaCode)){
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName('inventory_reservation');

            $columns = [
                ReservationInterfaceV2::STOCK_ID,
                ReservationInterfaceV2::SKU,
                ReservationInterfaceV2::QUANTITY,
                ReservationInterfaceV2::METADATA,
                ReservationInterfaceV2::AREA_CODE
            ];

            $data = [];
            /** @var ReservationInterfaceV2 $reservation */
            foreach ($reservations as $reservation) {
                $data[] = [
                    $reservation->getStockId(),
                    $reservation->getSku(),
                    $reservation->getQuantity(),
                    $reservation->getMetadata(),
                    $reservation->getAreaCode(),
                ];
            }
            $connection->insertArray($tableName, $columns, $data);
        } else {
            $result = $proceed($reservations);
            return $result;
        }
    }
}
