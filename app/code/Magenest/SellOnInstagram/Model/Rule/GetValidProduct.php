<?php

namespace Magenest\SellOnInstagram\Model\Rule;

use Magento\Catalog\Model\Config;
use Magento\CatalogRule\Model\Rule;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magenest\SellOnInstagram\Model\InstagramFeed;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class GetValidProduct
{

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var RuleFactory
     */
    protected $ruleFactory;
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var mixed|string
     */
    protected $itemsPerPage;

    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        RuleFactory $ruleFactory,
        Config $config,
        $itemsPerPage = ''
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->ruleFactory = $ruleFactory;
        $this->config = $config;
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * @param InstagramFeed $instagramFeed
     * @return array
     */
    public function execute(InstagramFeed $instagramFeed)
    {
        $page = 0;
        $validProducts = [];
        /** @var Rule $ruleModel */
        $ruleModel = $this->ruleFactory->create();
        $ruleModel->setConditionsSerialized($instagramFeed->getConditionsSerialized());
        $ruleModel->setStoreId($instagramFeed->getStoreId());

        $lastProductPage = false;
        do {
            $productCollection = $this->prepareCollection($instagramFeed, ++$page, $this->itemsPerPage);
            $ruleModel->getConditions()->collectValidatedAttributes($productCollection);
            if ($productCollection->getCurPage() >= $productCollection->getLastPageNumber()) {
                $lastProductPage = true;
            }
            $products = $productCollection->getItems();
            /** @var ProductInterface $product */
            foreach ($products as $product) {
                if ($this->validateProduct($ruleModel, $instagramFeed, $product)) {
                    $validProducts[] = $product->getId();
                }
            }
        } while (!$lastProductPage);

        return $validProducts;
    }

    /**
     * @param $instagramFeedModel
     * @param $page
     * @param $itemsPerPage
     * @param array $ids
     * @return Collection|AbstractDb
     */
    private function prepareCollection($instagramFeedModel, $page, $itemsPerPage, $ids = [])
    {
        $productCollection = $this->productCollectionFactory->create()
            ->addFieldToFilter(ProductInterface::STATUS, Status::STATUS_ENABLED)
            ->addStoreFilter($instagramFeedModel->getStoreId());
        if ($ids) {
            $productCollection->addAttributeToFilter('entity_id', ['in' => $ids]);
        }
        $productCollection->setPage($page, $itemsPerPage);

        return $productCollection;
    }

    public function validateProduct($ruleModel, $googleFeed, $product)
    {
        $product->setStoreId($googleFeed->getStoreId());
        if ($product->getAttributeSetId() != 4) {
            return $ruleModel->getConditions()->validate($product);
        }

        return $ruleModel->getConditions()->validate($product);
    }
}
