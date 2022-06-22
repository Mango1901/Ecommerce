<?php

namespace Magenest\VietnameseUrlKey\Model;

use Magenest\VietnameseUrlKey\Helper\Regenerate as RegenerateHelper;
use Magento\Catalog\Model\ResourceModel\Product\ActionFactory as ProductActionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGeneratorFactory;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGeneratorFactory;
use Magento\Framework\App\ResourceConnection;

class RegenerateProductRewrites extends AbstractRegenerateRewrites
{
    /**
     * @var string
     */
    protected $entityType = 'product';

    /**
     * @var int
     */
    protected $productsCollectionPageSize = 1000;

    /**
     * @var ProductActionFactory
     */
    protected $productActionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Action
     */
    protected $productAction;

    /**
     * @var ProductUrlRewriteGeneratorFactory
     */
    protected $productUrlRewriteGeneratorFactory;

    /**
     * @var \Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator
     */
    protected $productUrlRewriteGenerator;

    /**
     * @var ProductUrlPathGeneratorFactory
     */
    protected $productUrlPathGeneratorFactory;

    /**
     * @var \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator
     */
    protected $productUrlPathGenerator;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * RegenerateProductRewrites constructor.
     * @param RegenerateHelper $helper
     * @param ResourceConnection $resourceConnection
     * @param ProductActionFactory $productActionFactory
     * @param ProductUrlRewriteGeneratorFactory $productUrlRewriteGeneratorFactory
     * @param ProductUrlPathGeneratorFactory $productUrlPathGeneratorFactory
     * @param ProductCollectionFactory $productCollectionFactory
     */
    public function __construct(
        RegenerateHelper $helper,
        ResourceConnection $resourceConnection,
        ProductActionFactory $productActionFactory,
        \Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGeneratorFactory $productUrlRewriteGeneratorFactory,
        \Magento\CatalogUrlRewrite\Model\ProductUrlPathGeneratorFactory $productUrlPathGeneratorFactory,
        ProductCollectionFactory $productCollectionFactory
    ) {
        parent::__construct($helper, $resourceConnection);

        $this->productActionFactory = $productActionFactory;
        $this->productUrlRewriteGeneratorFactory = $productUrlRewriteGeneratorFactory;
        $this->productUrlPathGeneratorFactory = $productUrlPathGeneratorFactory;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * Regenerate Products Url Rewrites in specific store
     * @return $this
     */
    public function regenerate($storeId = 0)
    {
        if (count($this->regenerateOptions['productsFilter']) > 0) {
            $this->regenerateProductsRangeUrlRewrites(
                $this->regenerateOptions['productsFilter'],
                $storeId
            );
        } elseif (!empty($this->regenerateOptions['productId'])) {
            $this->regenerateProductUrlRewritesById(
                $this->regenerateOptions['productId'],
                $storeId
            );
        } elseif (!empty($this->regenerateOptions['productSku'])) {
            $this->regenerateProductUrlRewritesBySku(
                $this->regenerateOptions['productSku'],
                $storeId
            );
        } else {
            $this->regenerateAllProductsUrlRewrites($storeId);
        }

        return $this;
    }

    /**
     * Regenerate all products Url Rewrites
     * @param int $storeId
     * @return $this
     */
    public function regenerateAllProductsUrlRewrites($storeId = 0)
    {
        $this->regenerateProductsRangeUrlRewrites([], $storeId);

        return $this;
    }

    /**
     * Regenerate Url Rewrites for a specific product
     * @param int $productId
     * @param int $storeId
     * @return $this
     */
    public function regenerateProductUrlRewritesById($productId, $storeId = 0)
    {
        $this->regenerateProductsRangeUrlRewrites([$productId], $storeId);

        return $this;
    }

    public function regenerateProductUrlRewritesBySku($productSku, $storeId = 0)
    {
        $this->regenerateProductsRangeUrlRewrites([$productSku], $storeId, $typeFilter='sku');

        return $this;
    }

    /**
     * Regenerate Url Rewrites for a products range
     * @param array $productsFilter
     * @param int $storeId
     * @param string $typeFilter
     * @return $this
     */
    public function regenerateProductsRangeUrlRewrites($productsFilter = [], $storeId = 0, $typeFilter = 'id')
    {
        $products = $this->_getProductsCollection($productsFilter, $storeId, $typeFilter);
        $pageCount = $products->getLastPageNumber();
        $this->progressBarProgress = 1;
        $this->progressBarTotal = (int)$products->getSize();
        $currentPage = 1;

        while ($currentPage <= $pageCount) {
            $products->clear();
            $products->setCurPage($currentPage);

            foreach ($products as $product) {
                $this->_showProgress();
                $this->processProduct($product, $storeId);
            }

            $currentPage++;
        }

        $this->_updateSecondaryTable();

        return $this;
    }

    /**
     * Regenerate Url Rewrites for specific product in specific store
     * @param $entity
     * @param int $storeId
     * @return $this
     * @throws \Exception
     */
    public function processProduct($entity, $storeId = 0)
    {
        $entity->setStoreId($storeId)->setData('url_path', null);

        if ($this->regenerateOptions['saveOldUrls']) {
            $entity->setData('save_rewrites_history', true);
        }

        $updateAttributes = ['url_path' => null];

        $generatedKey = $this->_getProductUrlPathGenerator()->getUrlKey($entity->setUrlKey(null));
        $updateAttributes['url_key'] = $generatedKey;

        $this->_getProductAction()->updateAttributes(
            [$entity->getId()],
            $updateAttributes,
            $storeId
        );
        $urlRewrites = $this->_getProductUrlRewriteGenerator()->generate($entity);
        $urlRewrites = $this->helper->sanitizeProductUrlRewrites($urlRewrites);

        if (!empty($urlRewrites)) {
            $this->saveUrlRewrites(
                $urlRewrites,
                [['entity_type' => $this->entityType, 'entity_id' => $entity->getId(), 'store_id' => $storeId]]
            );
        }

        $this->progressBarProgress++;

        return $this;
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Action
     */
    protected function _getProductAction()
    {
        if (is_null($this->productAction)) {
            $this->productAction = $this->productActionFactory->create();
        }

        return $this->productAction;
    }

    /**
     * @return \Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator
     */
    protected function _getProductUrlRewriteGenerator()
    {
        if (is_null($this->productUrlRewriteGenerator)) {
            $this->productUrlRewriteGenerator = $this->productUrlRewriteGeneratorFactory->create();
        }

        return $this->productUrlRewriteGenerator;
    }

    /**
     * @return \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator
     */
    protected function _getProductUrlPathGenerator()
    {
        if (is_null($this->productUrlPathGenerator)) {
            $this->productUrlPathGenerator = $this->productUrlPathGeneratorFactory->create();
        }

        return $this->productUrlPathGenerator;
    }

    /**
     * Get products collection
     * @param array $productsFilter
     * @param int $storeId
     * @param $typeFilter
     * @return mixed
     */
    protected function _getProductsCollection($productsFilter = [], $storeId = 0, $typeFilter='id')
    {
        $productsCollection = $this->productCollectionFactory->create();

        $productsCollection->setStore($storeId)
            ->addStoreFilter($storeId)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('visibility')
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('url_path')
            // use limit to avoid a "eating" of a memory
            ->setPageSize($this->productsCollectionPageSize);

        if (count($productsFilter) > 0 && $typeFilter === 'id') {
            $productsCollection->addIdFilter($productsFilter);
        } elseif (count($productsFilter) > 0 && $typeFilter === 'sku') {
            $productsCollection->addFieldToFilter('sku', $productsFilter);
        }

        return $productsCollection;
    }
}
