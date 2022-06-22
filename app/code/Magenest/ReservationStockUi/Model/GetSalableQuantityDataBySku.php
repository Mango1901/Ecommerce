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

namespace Magenest\ReservationStockUi\Model;

use Magento\InventoryApi\Api\StockRepositoryInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magenest\CustomInventoryReservation\Api\GetProductSalableQtyInterfaceV2;
use Magenest\CustomInventoryReservation\Api\GetReservationsQuantityInterfaceV2;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventorySalesAdminUi\Model\ResourceModel\GetAssignedStockIdsBySku;

class GetSalableQuantityDataBySku extends \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku
{
    /**
     * @var GetProductSalableQtyInterfaceV2
     */
    private $getProductSalableQty;

    /**
     * @var StockRepositoryInterface
     */
    private $stockRepository;

    /**
     * @var GetAssignedStockIdsBySku
     */
    private $getAssignedStockIdsBySku;

    /**
     * @var GetStockItemConfigurationInterface
     */
    private $getStockItemConfiguration;

    /**
     * @var GetReservationsQuantityInterfaceV2
     */
    private $getReservationsQuantity;

    /**
     * GetSalableQuantityDataBySku constructor.
     *
     * @param GetReservationsQuantityInterfaceV2 $getReservationsQuantityV2
     * @param GetProductSalableQtyInterfaceV2 $getProductSalableQtyV2
     * @param GetProductSalableQtyInterface $getProductSalableQty
     * @param StockRepositoryInterface $stockRepository
     * @param GetAssignedStockIdsBySku $getAssignedStockIdsBySku
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     */
    public function __construct(
        GetReservationsQuantityInterfaceV2 $getReservationsQuantityV2,
        GetProductSalableQtyInterfaceV2 $getProductSalableQtyV2,
        GetProductSalableQtyInterface $getProductSalableQty,
        StockRepositoryInterface $stockRepository,
        GetAssignedStockIdsBySku $getAssignedStockIdsBySku,
        GetStockItemConfigurationInterface $getStockItemConfiguration
    ) {
        $this->getReservationsQuantity = $getReservationsQuantityV2;
        $this->getProductSalableQty = $getProductSalableQtyV2;
        $this->stockRepository = $stockRepository;
        $this->getAssignedStockIdsBySku = $getAssignedStockIdsBySku;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        parent::__construct($getProductSalableQty, $stockRepository, $getAssignedStockIdsBySku, $getStockItemConfiguration);
    }

    /**
     * @param string $sku
     *
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    public function execute(string $sku): array
    {
        $stockInfo = [];
        $stockIds = $this->getAssignedStockIdsBySku->execute($sku);
        if (count($stockIds)) {
            foreach ($stockIds as $stockId) {
                $qty = [];
                $reservation_qty = [];
                $stockId = (int)$stockId;
                $stock = $this->stockRepository->get($stockId);
                $stockItemConfiguration = $this->getStockItemConfiguration->execute($sku, $stockId);
                $isManageStock = $stockItemConfiguration->isManageStock();
                if ($this->isJson($this->getProductSalableQty->execute($sku, $stockId))) {
                    $productSalableQty = json_decode($this->getProductSalableQty->execute($sku, $stockId), true);
                    if (is_array($productSalableQty)) {
                        foreach ($productSalableQty as $key => $one) {
                            $qty[] = [
                                'key' => $key,
                                'qty' => $one
                            ];
                        }
                    } else {
                        $qty[] = [
                            'key' => '',
                            'qty' => 0
                        ];
                    }
                }
                if ($this->isJson($this->getReservationsQuantity->execute($sku, $stockId))) {
                    $reservationsQuantity = json_decode($this->getReservationsQuantity->execute($sku, $stockId), true);
                    if (is_array($reservationsQuantity)) {
                        foreach ($reservationsQuantity as $key => $one) {
                            $reservation_qty[] = [
                                'key' => $key,
                                'qty' => $one
                            ];
                        }
                    } else {
                        $reservation_qty[] = [
                            'key' => '',
                            'qty' => 0
                        ];
                    }
                }

                $stockInfo[] = [
                    'stock_name' => $stock->getName(),
                    'qty' => $isManageStock ? (!empty($qty) ? $qty : 0) : null,
                    'manage_stock' => $isManageStock,
                    'reservation_name' => __('Reservation (%1)', $stock->getName()),
                    'reservation_qty' => $isManageStock ? (!empty($reservation_qty) ? $reservation_qty : 0) : null
                ];
            }
        }

        return $stockInfo;
    }

    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
