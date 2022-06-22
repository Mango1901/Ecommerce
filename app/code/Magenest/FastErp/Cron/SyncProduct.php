<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 16/11/2021
 * Time: 13:23
 */

namespace Magenest\FastErp\Cron;

use Magenest\FastErp\Model\ClientFactory;
use Magenest\FastErp\Model\Flag\ProductsFactory;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Framework\Notification\NotifierInterface;
use Magento\Store\Model\StoreManagerInterface;

class SyncProduct
{
    protected $productFlag;

    protected $notifier;

    protected $client;

    protected $categoriesCache = [];

    protected $categoryCollection;

    protected $productFactory;

    protected $productResource;

    protected $categoryLink;

    protected $storeManager;

    protected $categoryFactory;

    protected $productRepository;

    /**
     * SyncProduct constructor.
     * @param ProductsFactory $productsFlagFactory
     * @param NotifierInterface $notifier
     * @param ClientFactory $clientFactory
     * @param CollectionFactory $collectionFactory
     * @param ProductFactory $productFactory
     * @param Product $productResource
     * @param CategoryLinkManagementInterface $categoryLink
     * @param StoreManagerInterface $storeManager
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        ProductsFactory $productsFlagFactory,
        NotifierInterface $notifier,
        ClientFactory $clientFactory,
        CollectionFactory $collectionFactory,
        ProductFactory $productFactory,
        Product $productResource,
        CategoryLinkManagementInterface $categoryLink,
        StoreManagerInterface $storeManager,
        CategoryFactory $categoryFactory,
        ProductRepositoryInterface $repository
    ) {
        $this->productFlag = $productsFlagFactory;
        $this->notifier = $notifier;
        $this->client = $clientFactory;
        $this->categoryCollection = $collectionFactory;
        $this->productFactory = $productFactory;
        $this->productResource = $productResource;
        $this->categoryLink = $categoryLink;
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
        $this->productRepository = $repository;
    }

    public function execute()
    {
        $flag = $this->productFlag->create()->loadSelf();
        if ($flag->getState() == 1) {
            $flag->setState(2)->save();
            $this->notifier->addNotice(
                'ERP Sync Product',
                "Start syncing products from ERP"
            );

            $products = $this->client->create()->getProducts();

            if (isset($products['data'])) {
                $index = 0;
                $total = $products['totalCount'];
                foreach ($products['data'] as $product) {
                    try {
                        $productModel = $this->productRepository->get($product['id']);
                    } catch (\Exception $exception) {
                        $productModel = $this->productFactory->create();
                    }
                    $productModel->addData([
                        'sku' => $product['id'],
                        'erp_model' => $product['model'],
                        'erp_unit' => $product['unitId'],
                        'weight' => isset($product['weight']) && $product['weight'] > 0 ? $product['weight'] : 5,
                        'length' => $product['length'] ? ($product['length'] / 1000) : 0,
                        'width' => $product['width'] ? ($product['width'] / 1000) : 0,
                        'height' => $product['height'] ? ($product['height'] / 1000) : 0,
                    ]);
                    $productModel
                        ->setStoreId(0)
                        ->setWebsiteIds([1]);
                    if (!$productModel->getId()) {
                        $productModel
                            ->setName($product['name'])
                            ->setAttributeSetId(4)
                            ->setStatus(0)
                            ->setVisibility(4)
                            ->setTaxClassId(0)
                            ->setTypeId('simple')
                            ->setPrice(0)
                            ->setStoreId(0)
                            ->setWebsiteIds([1]);
                    }
                    $this->productResource->save($productModel);
                    if (!$productModel->getId()) {
                        $this->assignCategories($product);
                    }
                    $index++;
                    if ($index % 100 == 0 || $index == $total) {
                        $this->notifier->addNotice(
                            'ERP Sync Product',
                            "Processing {$index}/{$total} syncing products from ERP"
                        );
                    }
                }
            }

            $flag->setState(0)->save();
            $this->notifier->addNotice(
                'ERP Sync Product',
                "Complete syncing products from ERP"
            );
        }
    }

    protected function assignCategories($product)
    {
        $categoryIds = [];
        if (isset($product['category'])) {
            $name = $product['category']['name'];
            $category = $this->getCategoriesByName($name);
            if (!$category) {
                $category = $this->createCategory($name);
            }
            $categoryIds[] = $category->getId();

            if (isset($product['subCategory'])) {
                $name = $product['subCategory']['name'];
                $subCategory = $this->getCategoriesByName($name);
                if (!$subCategory) {
                    $subCategory = $this->createSubCategory($category, $name);
                }
                $categoryIds[] = $subCategory->getId();
            }
        }

        if ($categoryIds) {
            $this->categoryLink->assignProductToCategories($product['id'], $categoryIds);
        }
    }

    protected function getCategoriesByName($name)
    {
        if (!isset($this->categoriesCache[$name])) {
            $collection = $this->categoryCollection->create();
            $collection->addAttributeToFilter('name', $name);
            $category = $collection->getFirstItem();
            $this->categoriesCache[$name] = $category->getId() ? $category : false;
        }

        return $this->categoriesCache[$name];
    }

    protected function createCategory($name)
    {
        $parentId = $this->storeManager->getStore()->getRootCategoryId();

        $parentCategory = $this->categoryFactory->create()->load($parentId);

        $category = $this->categoryFactory->create();
        $cate = $category->getCollection()
            ->addAttributeToFilter('name', $name)
            ->getFirstItem();

        if (!$cate->getId()) {
            $category->setPath($parentCategory->getPath())
                ->setParentId($parentId)
                ->setName($name)
                ->setIsActive(false);
            $category->save();
            $this->categoriesCache[$name] = $category;
        }
        return $category;
    }

    protected function createSubCategory($parentCategory, $name)
    {
        $category = $this->categoryFactory->create();
        $cate = $category->getCollection()
            ->addAttributeToFilter('name', $name)
            ->getFirstItem();

        if (!$cate->getId()) {
            $category->setPath($parentCategory->getPath())
                ->setParentId($parentCategory->getId())
                ->setName($name)
                ->setIsActive(false);
            $category->save();
            $this->categoriesCache[$name] = $category;
        }
        return $category;
    }
}
