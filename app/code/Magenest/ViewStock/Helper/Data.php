<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 17/12/2021
 * Time: 14:32
 */

namespace Magenest\ViewStock\Helper;

use Magenest\CustomSource\Model\Source\Area\Options;
use Magenest\ViewStock\Setup\Patch\Data\AddViewStockAttribute;
use Magento\Bundle\Api\ProductLinkManagementInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    /** @var StockResolverInterface */
    private $stockResolver;

    /** @var GetProductSalableQtyInterface */
    private $getProductSalableQty;

    /**
     * @var GetSourceItemsBySkuInterface
     */
    private $sourceItemsBySku;

    /**
     * @var ProductLinkManagementInterface
     */
    protected $productLinkManagement;

    protected $stockId;

    private $sources;

    private $area;

    private $product;

    private $customSource;

    /**
     * Data constructor.
     * @param Context $context
     * @param StockResolverInterface $stockResolver
     * @param GetProductSalableQtyInterface $getProductSalableQty
     * @param GetSourceItemsBySkuInterface $sourceItemsBySku
     * @param GetSourcesAssignedToStockOrderedByPriorityInterface $sources
     * @param StoreManagerInterface $storeManager
     * @param Options $options
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        Context $context,
        StockResolverInterface $stockResolver,
        GetProductSalableQtyInterface $getProductSalableQty,
        GetSourceItemsBySkuInterface $sourceItemsBySku,
        GetSourcesAssignedToStockOrderedByPriorityInterface $sources,
        StoreManagerInterface $storeManager,
        \Magenest\CustomSource\Helper\Data $customSource,
        Options $options,
        ProductLinkManagementInterface $productLinkManagement
    ) {
        $this->stockResolver = $stockResolver;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->sourceItemsBySku = $sourceItemsBySku;
        $this->sources = $sources;
        $this->area = $options;
        $this->customSource = $customSource;
        $this->productLinkManagement = $productLinkManagement;
        parent::__construct($context);
        $websiteCode = $storeManager->getWebsite()->getCode();
        $this->stockId = (int)$this->stockResolver->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode)->getStockId();
    }

    /**
     * @return float|int
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getQuantity()
    {
        $result = 0;
        $currentProduct = $this->getProduct();

        if ($currentProduct->getTypeId() === Configurable::TYPE_CODE) {
            $children = $currentProduct->getTypeInstance()->getUsedProducts($currentProduct);
            foreach ($children as $child) {
                $result += $this->getStockForCurrentWebsite($child->getSku());
            }
        } elseif (!$currentProduct->isComposite()) {
            $result = $this->getStockForCurrentWebsite($currentProduct->getSku());
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getProduct()->getData(AddViewStockAttribute::VIEW_STOCK) == Status::STATUS_ENABLED;
    }

    /**
     * @param $sku
     * @return float
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getStockForCurrentWebsite($sku)
    {
        return $this->getProductSalableQty->execute($sku, $this->stockId);
    }

    public function getQuantityBySource()
    {
        $product = $this->getProduct();
        $stockItems = [];
        $productNames = [];
        if (in_array($product->getTypeId(), ["configurable", "bundle", "grouped"])) {
            switch ($product->getTypeId()) {
                case "configurable":
                    $childProduct = $product->getTypeInstance()->getUsedProducts($product);
                    break;
                case "grouped":
                    $childProduct = $product->getTypeInstance()->getAssociatedProducts($product);
                    break;
                case "bundle":
                    $childProduct = $this->productLinkManagement->getChildren($product->getSku());
                    break;
            }

            foreach ($childProduct  as $child) {
                $stockItems[$child->getSku()] = $this->getSourceInfo($child);
                $productNames[$child->getSku()] = $child->getName();
            }
        } else {
            $stockItems[$product->getSku()] = $this->getSourceInfo($product);
            $productNames[$product->getSku()] = $product->getName();
        }

        $assignedSources = $this->sources->execute($this->stockId);
        $enableSources = [];
        foreach ($assignedSources as $source) {
            if ($source->isEnabled()) {
                $enableSources[$source->getSourceCode()] = $source;
            }
        }
        $result = [];
        $areas = $this->area->toOptionArray();
        $areasByCode = [];
        foreach ($areas as $area) {
            $areasByCode[$area['value']] = $area['label'];
        }

        foreach ($stockItems as $stockItem) {
            foreach ($stockItem as $stock) {
                if (isset($enableSources[$stock->getSourceCode()])) {
                    $source = $enableSources[$stock->getSourceCode()];
                    if (!isset($areasByCode[$source->getAreaCode()])) {
                        continue;
                    }
                    if (!isset($result[$source->getAreaCode()])) {
                        $result[$source->getAreaCode()] = [
                            'area' => $areasByCode[$source->getAreaCode()],
                            'skus' => []
                        ];
                    }
                    $qty = $stock->getStatus() == 1 && $stock->getQuantity() > 0 ? $stock->getQuantity() : 0;
                    if (isset($result[$source->getAreaCode()]['skus'][$stock->getSku()])) {
                        $result[$source->getAreaCode()]['skus'][$stock->getSku()]['quantity'] += $qty;
                    } else {
                        $result[$source->getAreaCode()]['skus'][$stock->getSku()] = [
                            'sku' => $stock->getSku(),
                            'quantity' => $qty,
                            'name' => $productNames[$stock->getSku()]
                        ];
                    }
                }
            }
        }
        return $result;
    }

    protected function getSourceInfo($product)
    {
        $isSalable = $this->getProductSalableQty->execute($product->getSku(), $this->stockId);
        //getting in-stock product
        if ($isSalable) {
            return $this->sourceItemsBySku->execute($product->getSku());
        }
        return [];
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return $this->getProduct()->getIdentities();
    }

    public function setProduct($product)
    {
        $this->product = $product;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function getCurrentArea()
    {
        return $this->customSource->getCurrentArea();
    }
}
