<?php

namespace Magenest\CustomSource\Plugin\Catalog;

use Magenest\CustomSource\Helper\Data;
use Magento\Bundle\Api\ProductLinkManagementInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Psr\Log\LoggerInterface;

/**
 * Class IsSalableProductInArea
 */
class IsSalableProductInArea
{
    /**
     * Customer session
     *
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var Data
     */
    protected $dataHelper;

    protected $cacheByProductId = [];

    /**
     * @var ProductLinkManagementInterface
     */
    protected $productLinkManagement;

    /**
     * @var StockItemRepository
     */
    protected $stockItemRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param HttpContext $httpContext
     * @param Data $dataHelper
     * @param ProductLinkManagementInterface $productLinkManagement
     */
    public function __construct(
        HttpContext $httpContext,
        Data $dataHelper,
        ProductLinkManagementInterface $productLinkManagement,
        StockItemRepository $stockItemRepository,
        LoggerInterface $logger
    ) {
        $this->httpContext = $httpContext;
        $this->dataHelper = $dataHelper;
        $this->productLinkManagement = $productLinkManagement;
        $this->stockItemRepository = $stockItemRepository;
        $this->logger = $logger;
    }

    /**
     * Allow display add to cart in area
     *
     * @param Product $subject
     * @param $result
     * @return bool|mixed
     */
    public function afterIsSalable(Product $subject, $result)
    {
        if ($result) {
            if (isset($this->cacheByProductId[$subject->getEntityId()])) {
                return $this->cacheByProductId[$subject->getEntityId()];
            } else {
                try {
                    $areaCode = $this->dataHelper->getCurrentArea();
                    if ($areaCode) {
                        $skus = [];
                        if ($subject->getTypeId() == "configurable") {
                            $childProduct = $subject->getTypeInstance()->getUsedProducts($subject);
                            $this->getSkuFromChild($childProduct, $skus);
                        } elseif ($subject->getTypeId() == 'grouped') {
                            $childProduct = $subject->getTypeInstance()->getAssociatedProducts($subject);
                            $this->getSkuFromChild($childProduct, $skus);
                        } elseif ($subject->getTypeId() == 'bundle') {
                            $childProduct = $this->productLinkManagement->getChildren($subject->getData('sku'));
                            $this->getSkuFromChild($childProduct, $skus);
                        } else {
                            $skus[] = $subject->getSku();
                        }

                        $inventorySourceItems = $this->dataHelper->getDataIsSalableOfProduct($areaCode, $skus);

                        $stockItem = null;

                        try {
                            if ($subject->getExtensionAttributes() &&
                                $subject->getExtensionAttributes()->getStockItem() &&
                                $subject->getExtensionAttributes()->getStockItem()->getItemId()
                            ) {
                                $stockItem = $this->stockItemRepository->get($subject->getExtensionAttributes()->getStockItem()->getItemId());
                            }
                        } catch (\Exception $exception) {
                            $this->logger->error($exception->getMessage());
                        }

                        foreach ($inventorySourceItems as $sourceItem) {
                            if ($sourceItem['is_in_stock']) {
                                $isAllowAddToCart = true;

                                if ($stockItem && $this->getConditionNotAllowAddToCart($sourceItem['qty'], $stockItem)) {
                                    $isAllowAddToCart = false;
                                }

                                $this->cacheByProductId[$subject->getEntityId()] = $isAllowAddToCart;
                                return $isAllowAddToCart;
                            }
                        }
                        $this->cacheByProductId[$subject->getEntityId()] = false;
                        return false;
                    }
                } catch (\Exception $exception) {
                    $this->logger->error($exception->getMessage());
                }
            }
        }
        // If not choose area or product is out of stock will not allow add to cart
        return false;
    }

    /**
     * @param array $childProduct
     * @param array $skus
     */
    private function getSkuFromChild($childProduct, &$skus)
    {
        foreach ($childProduct  as $child) {
            $skus[] = $child->getSku();
        }
    }

    /**
     * @param $sourceItemQty
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @return bool
     */
    private function getConditionNotAllowAddToCart($sourceItemQty, $stockItem)
    {
        // When enable backorder, value of min qty must less than or equal zero
        if ($stockItem->getBackorders()) {
            // When min qty = 0 and enable backorder, Magento allow infinity add to cart
            if ($stockItem->getMinQty() == 0) {
                return false;
            }

        }
        if (($sourceItemQty - $stockItem->getMinQty()) <= 0) {
            return true;
        }
        return false;
    }
}
