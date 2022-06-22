<?php
namespace Magenest\ViewStock\Block\Product;

use Magenest\CustomSource\Helper\Data as HelperData;
use Magenest\ViewStock\Setup\Patch\Data\AddViewStockAttribute;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View\AbstractView;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magenest\CustomSource\Model\Source\Area\Options;

class SalableQty extends AbstractView implements IdentityInterface
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
     * @var HelperData
     */
    private $helperData;

    protected $stockId;

    private $sources;

    private $area;

    /**
     * SalableQty constructor.
     * @param Context $context
     * @param ArrayUtils $arrayUtils
     * @param StockResolverInterface $stockResolver
     * @param GetProductSalableQtyInterface $getProductSalableQty
     * @param GetSourceItemsBySkuInterface $sourceItemsBySku
     * @param GetSourcesAssignedToStockOrderedByPriorityInterface $sources
     * @param Options $options
     * @param HelperData $helperData
     * @param array $data
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        StockResolverInterface $stockResolver,
        GetProductSalableQtyInterface $getProductSalableQty,
        GetSourceItemsBySkuInterface $sourceItemsBySku,
        GetSourcesAssignedToStockOrderedByPriorityInterface $sources,
        Options $options,
        HelperData $helperData,
        array $data = []
    ) {
        $this->stockResolver = $stockResolver;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->sourceItemsBySku = $sourceItemsBySku;
        $this->sources = $sources;
        $this->area = $options;
        $this->helperData = $helperData;
        parent::__construct($context, $arrayUtils, $data);
        $websiteCode = $this->_storeManager->getWebsite()->getCode();
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
            $childProduct = $product->getTypeInstance()->getAssociatedProducts($product);
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

    public function getCurrentArea()
    {
        return $this->helperData->getCurrentArea();
    }
}
