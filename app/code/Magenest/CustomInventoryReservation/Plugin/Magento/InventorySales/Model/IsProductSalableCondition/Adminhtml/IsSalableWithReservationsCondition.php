<?php
/**
 * Copyright © CustomInventoryReservation All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\CustomInventoryReservation\Plugin\Magento\InventorySales\Model\IsProductSalableCondition\Adminhtml;

use Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface;
use Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magenest\CustomInventoryReservation\Api\GetReservationsQuantityInterfaceV2;
use Magento\InventorySalesApi\Model\GetStockItemDataInterface;

class IsSalableWithReservationsCondition
{
    /**
     * @var GetStockItemDataInterface
     */
    private $getStockItemData;

    /**
     * @var GetReservationsQuantityInterface
     */
    private $getReservationsQuantity;

    /**
     * @var GetStockItemConfigurationInterface
     */
    private $getStockItemConfiguration;

    /**
     * @var IsSourceItemManagementAllowedForProductTypeInterface
     */
    private $isSourceItemManagementAllowedForProductType;

    /**
     * @var GetProductTypesBySkusInterface
     */
    private $getProductTypesBySkus;
    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @param GetStockItemDataInterface $getStockItemData
     * @param GetReservationsQuantityInterfaceV2 $getReservationsQuantity
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     * @param IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param GetProductTypesBySkusInterface $getProductTypesBySkus
     */
    public function __construct(
        GetStockItemDataInterface                            $getStockItemData,
        GetReservationsQuantityInterfaceV2                   $getReservationsQuantity,
        GetStockItemConfigurationInterface                   $getStockItemConfiguration,
        IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        \Magento\Framework\App\State                         $state,
        GetProductTypesBySkusInterface                       $getProductTypesBySkus
    )
    {
        $this->getStockItemData = $getStockItemData;
        $this->getReservationsQuantity = $getReservationsQuantity;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->state = $state;
        $this->getProductTypesBySkus = $getProductTypesBySkus;
    }

    public function aroundExecute(
        \Magento\InventorySales\Model\IsProductSalableCondition\IsSalableWithReservationsCondition $subject,
        \Closure                                                                                   $proceed,
        string                                                                                     $sku,
        int                                                                                        $stockId
    )
    {
        $qtySpecificArea = [];
        $productQtyInStock = [];
        $stockItemData = $this->getStockItemData->execute($sku, $stockId);
        if (empty($stockItemData)) {
            // Sku is not assigned to Stock
            return false;
        }

        $productType = $this->getProductTypesBySkus->execute([$sku])[$sku];
        foreach ($stockItemData as $data) {
            if (isset($qtySpecificArea[$data['area_code']])) {
                $qtySpecificArea[$data['area_code']] += $data["quantity"];
            } else {
                $qtySpecificArea[$data['area_code']] = $data["quantity"];
            }
        }
        if (false === $this->isSourceItemManagementAllowedForProductType->execute($productType)) {
            foreach ($stockItemData as $data) {
                return (bool)$data[GetStockItemDataInterface::IS_SALABLE];
            }
        }

        /** @var StockItemConfigurationInterface $stockItemConfiguration */
        $stockItemConfiguration = $this->getStockItemConfiguration->execute($sku, $stockId);

        foreach ($stockItemData as $data) {
            $reservationQtyArray = !is_float($this->getReservationsQuantity->execute($sku, $stockId, $data['area_code'])) ?? $this->isJson($this->getReservationsQuantity->execute($sku, $stockId, $data['area_code'])) ? json_decode($this->getReservationsQuantity->execute($sku, $stockId, $data['area_code']), true) : $this->getReservationsQuantity->execute($sku, $stockId, $data['area_code']);
            if ($data[GetStockItemDataInterface::IS_SALABLE] == 1) {
                if (!is_array($reservationQtyArray)) {
                    $productQtyInStock[$data['area_code']] = $qtySpecificArea[$data['area_code']]
                        + $reservationQtyArray;
                } else {
                    foreach ($reservationQtyArray as $reservationData) {
                        $productQtyInStock[$data['area_code']] = $qtySpecificArea[$data['area_code']]
                            + $reservationData;
                    }
                }
            }
        }
        $count = 0;
        $countArea = 0;
        foreach ($productQtyInStock as $one) {
            $countArea++;
            if ($one < $stockItemConfiguration->getMinQty()) {
                $count++;
            }
        }

        // if (all area reservation < minQty)
        if ($count == $countArea) {
            return false;
        }


        return true;

    }

    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
