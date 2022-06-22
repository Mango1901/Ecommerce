<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */

namespace Magenest\MegaMenu\Model\Source\Config;

class CategoryTree implements \Magento\Framework\Option\ArrayInterface
{
    static $tmp = [];

    protected $_storeManager;

    protected $_escaper = null;

    protected $_categoryFactory;

    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->_escaper = $escaper;
        $this->_categoryFactory = $categoryFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Options getter
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        $arr = [];
        foreach ($this->_storeManager->getGroups() as $store) {
            $categories = $this->getTreeCategories($store->getRootCategoryId());
            if (!empty($categories)) {
                $rootCat = $this->_categoryFactory->create()->load($store->getRootCategoryId());
                $arr[] = [
                    'label' => $rootCat->getName(),
                    'value' => $categories
                ];
            }
        }

        return $arr;
    }

    public function getOptionMenuItem()
    {
        $result = $this->toOptionArray();
        if (isset($result[0]) && isset($result[0]['value'])) {
            return $result[0]['value'];
        }

        return $result;
    }

    /**
     * @param $parentId
     * @param int $level
     * @param string $caret
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTreeCategories($parentId, $level = 0, $caret = ' _ ')
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $allCats */
        $allCats = $this->_categoryFactory->create()->getCollection();
        $allCats->addAttributeToSelect('*')
            ->addAttributeToFilter('is_active', '1')
            ->addAttributeToSort('position', 'asc');
        if ($parentId) {
            $allCats->addAttributeToFilter('parent_id', array('eq' => $parentId));
        }

        $prefix = "";
        if ($level) {
            $prefix = "|_";
            for ($i = 0; $i < $level; $i++) {
                $prefix .= $caret;
            }
        }
        foreach ($allCats as $category) {
            if (!isset(self::$tmp[$category->getId()])) {
                self::$tmp[$category->getId()] = $category->getId();
                $tmp["value"] = $category->getId();
                $tmp["label"] = $prefix . "(ID:" . $category->getId() . ") " . addslashes($category->getName());
                $arr[] = $tmp;
                $subcats = $category->getChildren();
                if ($subcats != '') {
                    $arr = array_merge($arr, $this->getTreeCategories($category->getId(), (int)$level + 1, $caret . ' _ '));
                }

            }

        }

        return isset($arr) ? $arr : array();
    }
}