<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 01/12/2021
 * Time: 15:36
 */

namespace Magenest\MegaMenu\Model\Config\Source;

class Category implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tree
     */
    protected $adminCategoryTree;

    /**
     * @var array
     */
    protected $options;

    /**
     * Category constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        \Magento\Catalog\Block\Adminhtml\Category\Tree $adminCategoryTree
    ) {
        $this->adminCategoryTree = $adminCategoryTree;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $collection = $this->adminCategoryTree->getTree();

            $this->processCategoryTree($collection, 0);
        }

        return $this->options;
    }

    protected function processCategoryTree($categoryChild, $level)
    {
        foreach ($categoryChild as $cat) {
            $this->options[] = [
                'label' => $this->_getSpaces($level) . ' ' . $cat['text'],
                'value' => $cat['id'],
            ];
            if (isset($cat['children'])) {
                $childrenLevel = $level;
                $this->processCategoryTree($cat['children'], ++$childrenLevel);
            }
        }
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }

    /**
     * Generate spaces
     * @param  int $n
     * @return string
     */
    protected function _getSpaces($n)
    {
        $s = '';
        for ($i = 0; $i < $n; $i++) {
            $s .= '_ _ _ _ ';
        }
        if ($s) {
            $s = "|" . $s;
        }
        return $s;
    }
}
