<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 */

namespace Magenest\MobileApi\Model\Catalog\Product;

use Magenest\MobileApi\Api\ProductGetSalableQtyInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySales\Model\ResourceModel\GetAssignedStockIdForWebsite;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class GetSalableQty
 * @package Magenest\MobileApi\Model\Catalog\Product
 */
class GetSalableQty implements ProductGetSalableQtyInterface
{
    protected $getSalableQtyInterface;

    protected $getAssignedStockId;

    protected $storeManager;

    protected $productRepository;

    public function __construct(
        GetProductSalableQtyInterface $getSalableQtyInterface,
        GetAssignedStockIdForWebsite $getAssignedStockId,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository
    ) {
        $this->getSalableQtyInterface = $getSalableQtyInterface;
        $this->getAssignedStockId     = $getAssignedStockId;
        $this->storeManager           = $storeManager;
        $this->productRepository      = $productRepository;
    }

    /**
     * @param string[] $ids
     *
     * @return array
     * @throws LocalizedException
     */
    public function getQty(array $ids)
    {
        $data           = [];
        $websiteCode    = $this->storeManager->getWebsite()->getCode();
        $websiteStockId = $this->getAssignedStockId->execute($websiteCode);
        foreach ($ids as $id) {
            try {
                $product   = $this->productRepository->getById($id);
                $qty       = $this->getSalableQtyInterface->execute($product->getSku(), $websiteStockId);
                $data[$id] = $qty ? intval($qty) : 0;
            } catch (NoSuchEntityException $e) {
            } catch (InputException $e) {
                $data[$id] = 0;
            } catch (LocalizedException $e) {
                $data[$id] = 0;
            }
        }

        return json_encode($data);
    }
}
