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

namespace Magenest\CustomCatalog\Helper;

use Magenest\CustomInventoryReservation\Rewrite\Magento\InventorySales\Model\Frontend\GetProductSalableQty;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventorySales\Model\ResourceModel\GetAssignedStockIdForWebsite;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Helper extends AbstractHelper
{
    const CONFIG_PATH_ENABLE_RELATED_GENERATION = 'catalog/related_generate/enable';
    const CONFIG_PATH_NUMBER_PRODUCT_GENERATION = 'catalog/related_generate/generate_amount';

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var GetProductSalableQty
     */
    private $getProductSalableQty;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var GetAssignedStockIdForWebsite
     */
    private $getAssignedStockIdForWebsite;

    /**
     * Helper constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param GetProductSalableQty $getProductSalableQty
     * @param LoggerInterface $logger
     * @param GetAssignedStockIdForWebsite $getAssignedStockIdForWebsite
     */
    public function __construct(
        Context      $context,
        StoreManagerInterface $storeManager,
        GetProductSalableQty $getProductSalableQty,
        LoggerInterface $logger,
        GetAssignedStockIdForWebsite $getAssignedStockIdForWebsite
    ) {
        $this->_storeManager = $storeManager;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->logger = $logger;
        $this->getAssignedStockIdForWebsite = $getAssignedStockIdForWebsite;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function isGenerationEnable()
    {
        return $this->getStoreConfig(self::CONFIG_PATH_ENABLE_RELATED_GENERATION);
    }

    /**
     * @param $path
     * @param $storeId
     * @return mixed
     */
    public function getStoreConfig($path, $storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = $this->getStoreId();
        }

        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        try {
            $storeId = $this->_storeManager->getStore()->getId();
        } catch (NoSuchEntityException $e) {
            $storeId = null;
        }

        return $storeId;
    }

    /**
     * @return mixed
     */
    public function getGeneratedProductNumber()
    {
        return $this->getStoreConfig(self::CONFIG_PATH_NUMBER_PRODUCT_GENERATION);
    }

    /**
     * @return bool
     */
    public function isSalableInArea($sku)
    {
        try {
            $salableQtyInArea = $this->getProductSalableQty->execute(
                $sku,
                $this->getStockIdForWebsite()
            );

            if ($salableQtyInArea > 0) {
                return false;
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return true;
    }

    /**
     * Get website code
     *
     * @return int|null
     */
    private function getStockIdForWebsite()
    {
        try {
            $websiteCode = $this->_storeManager->getWebsite()->getCode();
        } catch (LocalizedException $localizedException) {
            $websiteCode = null;
            $this->logger->error($localizedException->getMessage());
        }
        return $this->getAssignedStockIdForWebsite->execute($websiteCode);
    }
}
