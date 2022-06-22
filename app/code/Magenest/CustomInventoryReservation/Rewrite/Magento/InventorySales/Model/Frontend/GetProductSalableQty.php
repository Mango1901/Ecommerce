<?php
/**
 * Copyright © CustomInventoryReservation All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\CustomInventoryReservation\Rewrite\Magento\InventorySales\Model\Frontend;

use Magenest\CustomSource\Helper\Data;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magento\InventoryReservationsApi\Model\GetReservationsQuantityInterface;
use Magento\InventorySalesApi\Model\GetStockItemDataInterface;
use Magenest\CustomInventoryReservation\Api\GetReservationsQuantityInterfaceV2;

class GetProductSalableQty extends \Magento\InventorySales\Model\GetProductSalableQty
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
     * @var GetReservationsQuantityInterface
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
     * @var GetReservationsQuantityInterfaceV2
     */
    private $getReservationsQuantityV2;
    /**
     * @var Data
     */
    private $dataHelper;
    /**
     * @var \Magento\Framework\App\State
     */
    private $state;


    public function __construct(
        GetStockItemConfigurationInterface $getStockItemConfig,
        GetStockItemDataInterface $getStockItemData,
        GetReservationsQuantityInterface $getReservationsQuantity,
        IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        GetProductTypesBySkusInterface $getProductTypesBySkus,
        Data $dataHelper,
        \Magento\Framework\App\State $state,
        GetReservationsQuantityInterfaceV2 $getReservationsQuantityV2
    ){
        $this->getStockItemConfiguration = $getStockItemConfig;
        $this->getStockItemData = $getStockItemData;
        $this->getReservationsQuantity = $getReservationsQuantity;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->getProductTypesBySkus = $getProductTypesBySkus;
        $this->dataHelper = $dataHelper;
        $this->state = $state;
        $this->getReservationsQuantityV2 = $getReservationsQuantityV2;
        parent::__construct($getStockItemConfig, $getStockItemData, $getReservationsQuantity, $isSourceItemManagementAllowedForProductType, $getProductTypesBySkus);
    }

    /**
     * @inheritdoc
     */
    public function execute(string $sku, int $stockId): float
    {
        $areaCode = $this->dataHelper->getCurrentArea();
        //$this->validateProductType($sku);
        $stockItemData = $this->getStockItemData->execute($sku, $stockId);
        if (empty($stockItemData)) {
            return 0;
        }

        $stockItemConfig = $this->getStockItemConfiguration->execute($sku, $stockId);
        $minQty = $stockItemConfig->getMinQty();
        $reservationQtyArray = $this->getReservationsQuantityV2->execute($sku, $stockId, $areaCode);
        if ($stockItemData[\Magento\InventorySalesApi\Model\GetStockItemDataInterface::IS_SALABLE] == 1) {
            $productQtyInStock[$areaCode] = $stockItemData['quantity']
                + $reservationQtyArray
                - $minQty;
        } else {
            $productQtyInStock = 0;
        }

        $stock = $productQtyInStock[$areaCode] ?? $productQtyInStock;

        return (float)$stock;
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
}
