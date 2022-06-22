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

namespace Magenest\MegaMenu\Model;

class Menu extends \Magento\Framework\Model\AbstractExtensibleModel
{
    protected $productCollectionFactory;

    protected $catalogProductVisibility;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    protected $catalogCategoryHelper;

    protected $categoryRepo;

    protected $pageHelper;

    protected $categoryFactory;

    protected $catalogConfig;

    protected $imageBuilder;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $_eavConfig;

    /**
     * Menu constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Catalog\Block\Product\Context $catalogContext
     * @param \Magento\Catalog\Helper\Category $catalogCategory
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Cms\Helper\Page $pageHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Catalog\Block\Product\Context $catalogContext,
        \Magento\Catalog\Helper\Category $catalogCategory,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Cms\Helper\Page $pageHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $registry = $objectManager->create('\Magento\Framework\Registry');
        $localeDate = $objectManager->create('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $this->_eavConfig = $eavConfig;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->localeDate = $localeDate;
        $this->catalogCategoryHelper = $catalogCategory;
        $this->categoryRepo = $categoryRepository;
        $this->pageHelper = $pageHelper;
        $this->categoryFactory = $categoryFactory;
        $this->catalogConfig = $catalogContext->getCatalogConfig();
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
    }

    public function hasChildren()
    {
        $hasChild = false;
        $f1_level = (intval($this->getLevel()) + 1);
        $id = $this->getId();
        $collection = $this->getResourceCollection();
        $collection->addFieldToFilter('parent_id', $id)->addFieldToFilter('level', $f1_level)
            ->setOrder('sort', \Magento\Framework\DB\Select::SQL_ASC);
        if ($collection->getSize() > 0) {
            $hasChild = true;
        }

        return $hasChild;
    }

    public function getProductCollection($category)
    {
        $todayStartOfDayDate = $this->localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        $condFromDate = [
            'or' => [
                0 => [
                    'date' => true,
                    'to' => $todayEndOfDayDate,
                ], 1 => ['is' => new \Zend_Db_Expr('null')]
            ],
        ];
        $condToDate = [
            'or' => [
                0 => [
                    'date' => true,
                    'from' => $todayStartOfDayDate,
                ], 1 => ['is' => new \Zend_Db_Expr('null')]
            ],
        ];

        return $this->addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToFilter('news_from_date', $condFromDate, 'left')
            ->addAttributeToFilter('news_to_date', $condToDate, 'left')
            ->addAttributeToFilter(
                [
                    [
                        'attribute' => 'news_from_date',
                        'is' => new \Zend_Db_Expr('not null'),
                    ],
                    [
                        'attribute' => 'news_to_date',
                        'is' => new \Zend_Db_Expr('not null'),
                    ],
                ]
            )->addCategoryFilter($category)
            ->setPageSize($this->getProductsCount())
            ->setCurPage(1);
    }

    protected function addProductAttributesAndPrices(\Magento\Catalog\Model\ResourceModel\Product\Collection $collection)
    {
        return $collection->addMinimalPrice()->addTaxPercents()->addFinalPrice()
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())->addUrlRewrite();
    }

    /**
     * @return array|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAllChildren()
    {
        $out = $this->getF1Children();
        if ($out) {
            foreach ($out as $child) {
                $menu = $this->load($child['id']);
                $out[] = $menu->getAllChildren();
            }
        }

        return $out;
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getF1Children()
    {
        $f1_level = (intval($this->getLevel()) + 1);
        $id = $this->getId();

        return $this->getResourceCollection()->addFieldToFilter('parent_id', $id)->addFieldToFilter('level', $f1_level)
            ->setOrder('sort', \Magento\Framework\DB\Select::SQL_ASC);
    }

    protected function _construct()
    {
        $this->_init(\Magenest\MegaMenu\Model\ResourceModel\Menu::class);
    }
}
