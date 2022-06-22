<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_tn233 extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\LayeredNavigation\Model\Layer\Filter;

use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\DataProvider\Category as CategoryDataProvider;
use Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Category
 * @package Magenest\LayeredNavigation\Model\Layer\Filter
 */
class Category extends \Magento\CatalogSearch\Model\Layer\Filter\Category
{
    private $escaper;

    /**
     * @var CategoryDataProvider
     */
    private $dataProvider;
    /**
     * @var ItemBuilder
     */
    private $itemBuilder;
    /**
     * @var RequestInterface
     */
    private $request;

    private $appliedFilter;
    private $filterPlus;

    public function __construct(
        ItemFactory $filterItemFactory,
        StoreManagerInterface $storeManager,
        Layer $layer,
        DataBuilder $itemDataBuilder,
        Escaper $escaper,
        CategoryFactory $categoryDataProviderFactory,
        ItemBuilder $itemBuilder,
        RequestInterface $request,
        array $data = []
    ) {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $escaper, $categoryDataProviderFactory, $data);
        $this->escaper      = $escaper;
        $this->dataProvider = $categoryDataProviderFactory->create(['layer' => $this->getLayer()]);
        $this->itemBuilder  = $itemBuilder;
        $this->request      = $request;
    }

    public function getAppliedFilter()
    {
        return $this->appliedFilter;
    }

    public function apply(RequestInterface $request)
    {
        $categoryId = $request->getParam($this->_requestVar) ?: $request->getParam('id');
        if (empty($categoryId)) {
            return $this;
        }

        $categoryIds       = explode(',', $categoryId);
        $categoryIds       = array_unique($categoryIds);
        $productCollection = $this->getLayer()->getProductCollection();

        if ($request->getParam('id') != $categoryId) {
            $this->appliedFilter = $categoryId;
            if (!$this->filterPlus) {
                $this->filterPlus = true;
            }
            $productCollection->addCategoriesFilter(['in' => $categoryIds]);
            $category          = $this->getLayer()->getCurrentCategory();
            $child             = $category->getCollection()
                ->addFieldToFilter($category->getIdFieldName(), ['in' => $categoryIds])
                ->addAttributeToSelect('name');
            $categoriesInState = [];
            foreach ($categoryIds as $categoryId) {
                if ($currentCategory = $child->getItemById($categoryId)) {
                    $categoriesInState[$currentCategory->getId()] = $currentCategory->getName();
                }
            }
            foreach ($categoriesInState as $key => $category) {
                $state = $this->_createItem($category, $key);
                $this->getLayer()->getState()->addFilter($state);
            }
        }
        return $this;
    }

    protected function _initItems()
    {
        $itemsData = $this->_getItemsData();
        $itemList  = [];
        foreach ($itemsData as $itemData) {
            $itemList[] = $this->_createItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['count'],
                $itemData['active'],
                $itemData['plus']
            );
        }

        $this->_items = $itemList;
        return $this;
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        /** @var Collection $productCollection */
        $productCollection  = $this->getLayer()->getProductCollection();
        $optionsFacetedData = $productCollection->getFacetedData('category');
        $category           = $this->dataProvider->getCategory();
        $categories         = $category->getChildrenCategories();

        $collectionSize      = $productCollection->getSize();
        $this->appliedFilter = $this->request->getParam($this->_requestVar) ?: $this->request->getParam('id');
        $activeFilters       = [];
        if ($this->appliedFilter) {
            $activeFilters = explode(',', (string)$this->appliedFilter);
        }
        if ($category->getIsActive()) {
            foreach ($categories as $category) {
                if ($category->getIsActive()
                    && isset($optionsFacetedData[$category->getId()])
                ) {
                    $active = in_array($category->getId(), $activeFilters);
                    $this->itemBuilder->addItemData(
                        $this->escaper->escapeHtml($category->getName()),
                        $category->getId(),
                        $optionsFacetedData[$category->getId()]['count'] ?? 0,
                        $active,
                        $this->filterPlus
                    );
                }
            }
        }

        return $this->itemBuilder->build();
    }

    protected function _createItem($itemLabel, $itemValue, $itemCount = 0, $active = false, $plus = false)
    {
        return $this->_filterItemFactory->create()
            ->setFilter($this)
            ->setLabel($itemLabel)
            ->setValue($itemValue)
            ->setCount($itemCount)
            ->setActive($active)
            ->setPlus($plus);
    }
}
