<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\CustomInventoryReservation\Model\ResourceModel;

use Magenest\CustomInventoryReservation\Api\GetReservationsQuantityInterfaceV2;
use Magenest\CustomInventoryReservation\Api\ReservationInterfaceV2;
use Magenest\CustomSource\Helper\Data;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ResourceConnection;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use \Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class GetReservationsQuantity implements GetReservationsQuantityInterfaceV2
{
    CONST AREA_CODE = ["mien_bac","mien_trung","mien_nam"];
    /**
     * @var Data
     */
    private $dataHelper;
    /**
     * @var SourceRepositoryInterface
     */
    private $sourceRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(
        Data $dataHelper,
        SourceRepositoryInterface $sourceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger,
        \Magento\Framework\App\State $state,
        ResourceConnection $resourceConnection
    ) {
        $this->dataHelper = $dataHelper;
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
        $this->state = $state;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $sku, int $stockId, string $areaCode = null)
    {
        $areaQtyReservation = [];

        if ($this->state->getAreaCode() == 'frontend') {
            $areaCode = $this->dataHelper->getCurrentArea();
            $connection = $this->resourceConnection->getConnection();
            $reservationTable = $this->resourceConnection->getTableName('inventory_reservation');
            $select = $connection->select()
                ->from($reservationTable, [ReservationInterfaceV2::QUANTITY => 'SUM(' . ReservationInterfaceV2::QUANTITY . ')'])
                ->where(ReservationInterfaceV2::SKU . ' = ?', $sku)
                ->where(ReservationInterfaceV2::STOCK_ID . ' = ?', $stockId)
                ->where(ReservationInterfaceV2::AREA_CODE . ' = ?', $areaCode)
                ->limit(1);

            $reservationQty = $connection->fetchOne($select);
            if (false === $reservationQty) {
                $reservationQty = 0;
            }
            return (float)$reservationQty;
        }

        if ($this->state->getAreaCode() == 'adminhtml') {
            if (isset($areaCode)) {
                $connection = $this->resourceConnection->getConnection();
                $reservationTable = $this->resourceConnection->getTableName('inventory_reservation');
                $select = $connection->select()
                    ->from($reservationTable, [
                        ReservationInterfaceV2::QUANTITY => 'SUM(' . ReservationInterfaceV2::QUANTITY . ')'
                    ])
                    ->where(ReservationInterfaceV2::SKU . ' = ?', $sku)
                    ->where(ReservationInterfaceV2::STOCK_ID . ' = ?', $stockId)
                    ->where(ReservationInterfaceV2::AREA_CODE . ' = ?', $areaCode);
                $reservationQty = $connection->fetchOne($select);
                if ($reservationQty != null) {
                    $areaQtyReservation[$areaCode] = $reservationQty;
                }
                if (empty($areaQtyReservation)) {
                    $areaQtyReservation[$areaCode] = 0;
                } else {
                    if (!array_key_exists($areaCode, $areaQtyReservation)) {
                        $areaQtyReservation[$areaCode] = 0;
                    }
                }
                return json_encode($areaQtyReservation);
            } else {
                $areaCodes = $this->dataHelper->getAreaCodeBySku($sku);
                foreach ($areaCodes as $areaCode) {
                    $connection = $this->resourceConnection->getConnection();
                    $reservationTable = $this->resourceConnection->getTableName('inventory_reservation');
                    $select = $connection->select()
                        ->from($reservationTable, [
                            ReservationInterfaceV2::QUANTITY => 'SUM(' . ReservationInterfaceV2::QUANTITY . ')'
                        ])
                        ->where(ReservationInterfaceV2::SKU . ' = ?', $sku)
                        ->where(ReservationInterfaceV2::STOCK_ID . ' = ?', $stockId)
                        ->where(ReservationInterfaceV2::AREA_CODE . ' = ?', $areaCode);
                    $reservationQty = $connection->fetchOne($select);
                    if ($reservationQty != null) {
                        $areaQtyReservation[$areaCode] = $reservationQty;
                    }
                    if (empty($areaQtyReservation)) {
                        $areaQtyReservation[$areaCode] = 0;
                    } else {
                        if (!array_key_exists($areaCode, $areaQtyReservation)) {
                            $areaQtyReservation[$areaCode] = 0;
                        }
                    }
                }
                return json_encode($areaQtyReservation);
            }
        }
    }
}
