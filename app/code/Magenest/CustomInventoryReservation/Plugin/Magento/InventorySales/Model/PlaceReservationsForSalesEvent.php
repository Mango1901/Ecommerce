<?php
/**
 * Copyright © CustomInventoryReservation All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\CustomInventoryReservation\Plugin\Magento\InventorySales\Model;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\InventoryReservationsApi\Model\AppendReservationsInterface;
use Magento\InventorySalesApi\Api\Data\ItemToSellInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\Data\SalesEventInterface;
use Magenest\CustomSource\Helper\Data;
use Magento\InventorySalesApi\Api\GetStockBySalesChannelInterface;
use Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magenest\CustomInventoryReservation\Api\ReservationBuilderInterfaceV2;
use Magento\InventorySales\Model\SalesEventToArrayConverter;

class PlaceReservationsForSalesEvent
{
    /**
     * @var Data
     */
    private $dataHelper;
    /**
     * @var GetStockBySalesChannelInterface
     */
    private $getStockBySalesChannel;
    /**
     * @var GetProductTypesBySkusInterface
     */
    private $getProductTypesBySkus;
    /**
     * @var IsSourceItemManagementAllowedForProductTypeInterface
     */
    private $isSourceItemManagementAllowedForProductType;
    /**
     * @var ReservationBuilderInterfaceV2
     */
    private $reservationBuilder;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var SalesEventToArrayConverter
     */
    private $salesEventToArrayConverter;

    /**
     * @var AppendReservationsInterface
     */
    private $appendReservations;
    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @param Data $dataHelper
     * @param GetStockBySalesChannelInterface $getStockBySalesChannel
     * @param GetProductTypesBySkusInterface $getProductTypesBySkus
     * @param IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param ReservationBuilderInterfaceV2 $reservationBuilder
     * @param SerializerInterface $serializer
     * @param SalesEventToArrayConverter $salesEventToArrayConverter
     * @param \Magento\Framework\App\State $state
     * @param AppendReservationsInterface $appendReservations
     */
    public function __construct(
        Data $dataHelper,
        GetStockBySalesChannelInterface $getStockBySalesChannel,
        GetProductTypesBySkusInterface $getProductTypesBySkus,
        IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        ReservationBuilderInterfaceV2 $reservationBuilder,
        SerializerInterface $serializer,
        SalesEventToArrayConverter $salesEventToArrayConverter,
        \Magento\Framework\App\State $state,
        AppendReservationsInterface $appendReservations
    ){
        $this->dataHelper = $dataHelper;
        $this->getStockBySalesChannel = $getStockBySalesChannel;
        $this->getProductTypesBySkus = $getProductTypesBySkus;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->reservationBuilder = $reservationBuilder;
        $this->serializer = $serializer;
        $this->salesEventToArrayConverter = $salesEventToArrayConverter;
        $this->state = $state;
        $this->appendReservations = $appendReservations;
    }

    public function aroundExecute(
        \Magento\InventorySales\Model\PlaceReservationsForSalesEvent $subject,
        \Closure $proceed,
        array $items,
        SalesChannelInterface $salesChannel,
        SalesEventInterface $salesEvent
    ) {
        $areaCode = $this->dataHelper->getCurrentArea();
        if (isset($areaCode)) {
            if (empty($items)) {
                return;
            }

            $stockId = $this->getStockBySalesChannel->execute($salesChannel)->getStockId();

            $skus = [];
            /** @var ItemToSellInterface $item */
            foreach ($items as $item) {
                $skus[] = $item->getSku();
            }
            $productTypes = $this->getProductTypesBySkus->execute($skus);

            $reservations = [];
            /** @var ItemToSellInterface $item */
            foreach ($items as $item) {
                $currentSku = $item->getSku();
                $skuNotExistInCatalog = !isset($productTypes[$currentSku]);
                if ($skuNotExistInCatalog ||
                    $this->isSourceItemManagementAllowedForProductType->execute($productTypes[$currentSku])) {
                    $reservations[] = $this->reservationBuilder
                        ->setSku($item->getSku())
                        ->setQuantity((float)$item->getQuantity())
                        ->setStockId($stockId)
                        ->setMetadata($this->serializer->serialize($this->salesEventToArrayConverter->execute($salesEvent)))
                        ->setAreaCode($areaCode)
                        ->build();
                }
            }
            $this->appendReservations->execute($reservations);
        } else {
            $result = $proceed($items, $salesChannel, $salesEvent);
            return $result;
        }

    }
}
