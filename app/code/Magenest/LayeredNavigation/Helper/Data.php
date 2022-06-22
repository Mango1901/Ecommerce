<?php

namespace Magenest\LayeredNavigation\Helper;

use Magento\Catalog\Helper\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Indexer\Category\Flat\State;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Data
 * @package Magenest\LayeredNavigation\Helper
 */
class Data extends AbstractHelper
{
    protected $productCollection;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    protected $layout;

    protected $swatchHelper;

    protected $categoryFactory;

    protected $productFactory;

    protected $_coreRegistry;

    /**
     * @var State
     */
    protected $categoryFlatConfig;

    protected $categoryHelper;

    public function __construct(
        Collection $productCollection,
        StoreManagerInterface $storeManager,
        LayoutInterface $layout,
        \Magento\Swatches\Helper\Data $swatchHelper,
        CategoryFactory $categoryFactory,
        ProductFactory $productFactory,
        Registry $registry,
        State $categoryFlatState,
        Category $categoryHelper,
        Context $context
    ) {
        $this->productCollection  = $productCollection;
        $this->_storeManager      = $storeManager;
        $this->layout             = $layout;
        $this->swatchHelper       = $swatchHelper;
        $this->categoryFactory    = $categoryFactory;
        $this->productFactory     = $productFactory;
        $this->_coreRegistry      = $registry;
        $this->categoryFlatConfig = $categoryFlatState;
        $this->categoryHelper     = $categoryHelper;
        parent::__construct($context);
    }

    public function getMaxPriceByCategory($currentCategory)
    {
        $collection = $this->productCollection->addCategoryFilter($currentCategory)
            ->addAttributeToSort('price', 'desc');
        return $collection->getMaxPrice() + 1;
    }

    public function getAllCategory($sorted = false, $asCollection = false, $toLoad = true)
    {
        $category        = $this->categoryFactory->create();
        $storeCategories = $category->getCategories(1, $recursionLevel = 1, $sorted, $asCollection, $toLoad);
        return $storeCategories;
    }

    public function getChildCategoryView($category, $html = '', $level = 0)
    {
        // Check if category has children
        if ($category->hasChildren()) {
            $childCategories = $this->categoryFactory->create()->getCollection()
                ->addAttributeToSelect('category_icon')
                ->addAttributeToSelect("include_in_menu")
                ->addAttributeToFilter("include_in_menu", 1)
                ->addFieldToSelect('name')
                ->setOrder('position')
                ->addFieldToFilter('entity_id', ['in' => $this->getSubcategories($category)]);

            if (count($childCategories) > 0) {
                $html .= '<ul class="o-list o-list--unstyled">';

                // Loop through children categories
                foreach ($childCategories as $childCategory) {
                    $html .= '<li class="level' . $level . ($this->isActive($childCategory) ? ' active' : '') . '">';
                    if ($childCategory->getImageUrl('category_icon')) {
                        $img = $childCategory->getImageUrl('category_icon');
                        $html .= "<img src='$img' height = '12' width = '12' class='category-icon' >";
                    }
                    $html .= '<a href="' . $this->getCategoryUrl($childCategory) . '" title="' . $childCategory->getName() . '" class="' . ($this->isActive($childCategory) ? 'is-active' : '') . '">' . $childCategory->getName() . '</a>';

                    if ($childCategory->hasChildren()) {
                        if ($this->isActive($childCategory)) {
                            $html .= '<span class="expanded"><i class="fa fa-angle-up"></i></span>';
                        } else {
                            $html .= '<span class="expand"><i class="fa fa-angle-down"></i></span>';
                        }
                    }

                    if ($childCategory->hasChildren()) {
                        $html .= $this->getChildCategoryView($childCategory, '', ($level + 1));
                    }

                    $html .= '</li>';
                }
                $html .= '</ul>';
            }
        }

        return $html;
    }

    /**
     * Retrieve subcategories
     *
     * @param $category
     *
     * @return array
     */
    public function getSubcategories($category)
    {
        if ($this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource()) {
            return (array)$category->getChildrenNodes();
        }

        return $category->getChildren();
    }

    /**
     * @param $category
     * @return bool
     */
    public function isActive($category)
    {
        $activeCategory = $this->_coreRegistry->registry('current_category');
        $activeProduct  = $this->_coreRegistry->registry('current_product');

        if (!$activeCategory) {

            // Check if we're on a product page
            if ($activeProduct !== null) {
                return in_array($category->getId(), $activeProduct->getCategoryIds());
            }

            return false;
        }

        // Check if this is the active category
        if ($this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource() and
            $category->getId() == $activeCategory->getId()
        ) {
            return true;
        }

        // Check if a subcategory of this category is active
        $childrenIds = $category->getAllChildren(true);
        if (!is_null($childrenIds) and in_array($activeCategory->getId(), $childrenIds)) {
            return true;
        }

        // Fallback - If Flat categories is not enabled the active category does not give an id
        return (($category->getName() == $activeCategory->getName()) ? true : false);
    }

    /**
     * Return Category Id for $category object
     *
     * @param $category
     *
     * @return string
     */
    public function getCategoryUrl($category)
    {
        return $this->categoryHelper->getCategoryUrl($category);
    }
}
