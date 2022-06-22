<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\CustomInventoryReservation\Model;

use Magenest\CustomInventoryReservation\Api\GetProductSalableQtyInterfaceV2;
use Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magenest\CustomInventoryReservation\Api\GetReservationsQuantityInterfaceV2;
use Magento\InventorySalesApi\Model\GetStockItemDataInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magenest\CustomSource\Helper\Data;

/**
 * @inheritdoc
 */
class GetProductSalableQty implements GetProductSalableQtyInterfaceV2
{
    /**
     * @var GetStockItemConfigurationInterface
     */
    private $getStockItemConfiguration;

    /**
     * @var GetStockItemDataInterface
     */
    private $getStockItemData;

    /**
     * @var GetReservationsQuantityInterfaceV2
     */
    private $getReservationsQuantity;

    /**
     * @var IsSourceItemManagementAllowedForProductTypeInterface
     */
    private $isSourceItemManagementAllowedForProductType;

    /**
     * @var GetProductTypesBySkusInterface
     */
    private $getProductTypesBySkus;
    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @param GetStockItemConfigurationInterface $getStockItemConfig
     * @param GetStockItemDataInterface $getStockItemData
     * @param GetReservationsQuantityInterfaceV2 $getReservationsQuantity
     * @param IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param Data $dataHelper
     * @param GetProductTypesBySkusInterface $getProductTypesBySkus
     */
    public function __construct(
        GetStockItemConfigurationInterface $getStockItemConfig,
        GetStockItemDataInterface $getStockItemData,
        GetReservationsQuantityInterfaceV2 $getReservationsQuantity,
        IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        Data $dataHelper,
        GetProductTypesBySkusInterface $getProductTypesBySkus
    ) {
        $this->getStockItemConfiguration = $getStockItemConfig;
        $this->getStockItemData = $getStockItemData;
        $this->getReservationsQuantity = $getReservationsQuantity;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->getProductTypesBySkus = $getProductTypesBySkus;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $sku, int $stockId)
    {
        $qtySpecificArea = [];
        $productQtyInStock = [];
        $this->validateProductType($sku);
        $stockItemData = $this->getStockItemData->execute($sku, $stockId);
        $stockItemConfig = $this->getStockItemConfiguration->execute($sku, $stockId);
        $minQty = $stockItemConfig->getMinQty();

        if (empty($stockItemData)) {
            return 0;
        }
        foreach ($stockItemData as $data) {
            if (isset($qtySpecificArea[$data['area_code']])) {
                $qtySpecificArea[$data['area_code']] += $data["quantity"];
            } else {
                $qtySpecificArea[$data['area_code']] = $data["quantity"];
            }
        }
        foreach($stockItemData as $data) {
            $reservationQtyArray = json_decode($this->getReservationsQuantity->execute($sku, $stockId, $data['area_code']), true);
            if ($data[GetStockItemDataInterface::IS_SALABLE] == 1) {
                if (!is_array($reservationQtyArray)) {
                    $productQtyInStock[$data['area_code']] = $qtySpecificArea[$data['area_code']]
                        + $reservationQtyArray
                        - $minQty;
                } else {
                    foreach ($reservationQtyArray as $reservationData) {
                        $productQtyInStock[$data['area_code']] = $qtySpecificArea[$data['area_code']]
                            + $reservationData
                            - $minQty;
                    }
                }
            }
        }
        if (empty($productQtyInStock)) {
            return 0;
        } else {
            return json_encode($productQtyInStock);
        }
    }

    /**
     * Check if source can be managed for a product of specific type.
     *
     * @param string $sku
     * @throws InputException
     */
    private function validateProductType(string $sku): void
    {
        $productTypesBySkus = $this->getProductTypesBySkus->execute([$sku]);
        if (!array_key_exists($sku, $productTypesBySkus)) {
            throw new NoSuchEntityException(
                __('The product that was requested doesn\'t exist. Verify the product and try again.')
            );
        }

        $productType = $productTypesBySkus[$sku];

        if (false === $this->isSourceItemManagementAllowedForProductType->execute($productType)) {
            throw new InputException(
                __('Can\'t check requested quantity for products without Source Items support.')
            );
        }
    }

    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
