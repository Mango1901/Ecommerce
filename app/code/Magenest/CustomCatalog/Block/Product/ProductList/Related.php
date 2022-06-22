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

namespace Magenest\CustomCatalog\Block\Product\ProductList;

use Magento\Catalog\Model\Category;

class Related extends \Magento\Catalog\Block\Product\ProductList\Related
{
    protected $categoryFactory;

    protected $categoryResource;

    protected $helper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Checkout\Model\ResourceModel\Cart $checkoutCart,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Category $categoryResource,
        \Magenest\CustomCatalog\Helper\Helper $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->categoryFactory = $categoryFactory;
        $this->categoryResource = $categoryResource;
        parent::__construct($context, $checkoutCart, $catalogProductVisibility, $checkoutSession, $moduleManager, $data);
    }

    /**
     * Prepare data
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareData()
    {
        $product = $this->getProduct();
        /* @var $product \Magento\Catalog\Model\Product */

        $this->_itemCollection = $product->getRelatedProductCollection()->addAttributeToSelect(
            'required_options')->setPositionOrder()->addStoreFilter();

        if ($this->moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
        $this->_itemCollection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());

        $this->_itemCollection->load();
        if (true || $this->helper->isGenerationEnable()) {
            $generatedRelated = $this->generatedRelatedProduct($product, $this->_itemCollection->getAllIds());
            foreach ($generatedRelated as $item) {
                $this->_itemCollection->addItem($item);
            }
        }

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    public function generatedRelatedProduct(\Magento\Catalog\Model\Product $product, $existedIds)
    {
        $categoryIds = $product->getCategoryIds();
        $loadedCategory = false;
        $productCount = 0;
        while ($categoryId = (int)array_pop($categoryIds)) {
            if ($categoryId <= Category::ROOT_CATEGORY_ID) {
                continue;
            }
            $category = $this->categoryFactory->create();
            $this->categoryResource->load($category, $categoryId);
            if ($category->getProductCount() > 0 && $category->getProductCount() > $productCount) {
                $loadedCategory = $category;
                $productCount = $category->getProductCount();
            }
            if ($productCount >= $this->helper->getGeneratedProductNumber()) {
                break;
            }
        }
        if (!$loadedCategory || !($loadedCategory instanceof \Magento\Catalog\Model\Category)) {
            return [];
        }
        $productCol = $loadedCategory->getProductCollection();
        if(is_array($existedIds) && count($existedIds)>0){
            $productCol->addFieldToFilter('entity_id', ['nin' => $existedIds]);
        }
        if ($this->moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($productCol);
        }
        $productCol->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $productCol->setPageSize($this->helper->getGeneratedProductNumber())->setCurPage(1);

        return $productCol;
    }
}
