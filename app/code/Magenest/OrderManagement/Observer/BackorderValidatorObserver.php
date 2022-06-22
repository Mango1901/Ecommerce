<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kangaroo extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\OrderManagement\Observer;

use Magento\Quote\Model\Quote\Item;
use Magento\Framework\Event\Observer;
use Magenest\OrderManagement\Model\Order;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Model\GetStockItemDataInterface;
use Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface;

class BackorderValidatorObserver implements ObserverInterface
{
    /**
     * @var GetSkusByProductIdsInterface
     */
    private $getSkusByProductIds;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StockResolverInterface
     */
    private $stockResolver;

    /**
     * @var GetStockItemConfigurationInterface
     */
    private $getStockItemConfiguration;

    /**
     * @var GetStockItemDataInterface
     */
    private $getStockItemData;

    private $logger;

    /**
     * BackorderValidatorObserver constructor.
     *
     * @param GetSkusByProductIdsInterface $skusByProductIds
     * @param StoreManagerInterface $storeManager
     * @param StockResolverInterface $stockResolver
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     * @param GetStockItemDataInterface $getStockItemData
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        GetSkusByProductIdsInterface $skusByProductIds,
        StoreManagerInterface $storeManager,
        StockResolverInterface $stockResolver,
        GetStockItemConfigurationInterface $getStockItemConfiguration,
        GetStockItemDataInterface $getStockItemData,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->getSkusByProductIds = $skusByProductIds;
        $this->storeManager = $storeManager;
        $this->stockResolver = $stockResolver;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        $this->getStockItemData = $getStockItemData;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /* @var $quoteItem Item */
        $quoteItem = $observer->getEvent()->getItem();
        if ($quoteItem && $quoteItem->getProductId() && $quoteItem->getQuote()) {
            $product = $quoteItem->getProduct();
            try {
                $skus = $this->getSkusByProductIds->execute([$product->getId()]);
                $productSku = $skus[$product->getId()];

                $websiteCode = $this->storeManager->getWebsite($product->getStore()->getWebsiteId())->getCode();
                $stock = $this->stockResolver->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode);
                $stockId = $stock->getStockId();

                $stockItemConfiguration = $this->getStockItemConfiguration->execute($productSku, $stockId);

                if ($stockItemConfiguration->getBackorders() === StockItemConfigurationInterface::BACKORDERS_YES_NOTIFY) {
                    $stockItemData = $this->getStockItemData->execute($productSku, $stockId);
                    if (null === $stockItemData) {
                        return;
                    }

                    $backOrderQty = $quoteItem->getQty() - $stockItemData[GetStockItemDataInterface::QUANTITY];
                    if ($backOrderQty > 0) {
                        $quoteItem->setData(Order::ORDER_ITEM_IS_BACKORDER, $backOrderQty);
                    }
                }
            } catch (NoSuchEntityException $e) {
                $this->logger->debug($e->getMessage());
            } catch (LocalizedException $e) {
                $this->logger->debug($e->getMessage());
            }
        }

        return;
    }
}
