<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Catalog\Widget;

use Magento\Catalog\Block\Product\Widget\NewWidgetFactory;
use Magenest\MobileApi\Model\Catalog\WidgetAbstract;
use Magento\Framework\EntityManager\Operation\Read\ReadExtensions;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\Config as CatalogConfig;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

/**
 * Class NewProduct
 * @package Magenest\MobileApi\Model\Catalog\Widget
 */
class NewProduct extends WidgetAbstract
{
    /**
     * @var NewWidgetFactory
     */
    protected $_newWidgetFactory;

    /**
     * @var ProductSliderFactory
     */
    protected $_productSliderFactory;

    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * @var CatalogConfig
     */
    protected $_catalogConfig;

    /**
     * Constructor.
     *
     * @param ReadExtensions $readExtensions
     * @param JoinProcessorInterface $joinProcessor
     * @param NewWidgetFactory $newWidgetFactory
     * @param ProductSliderFactory $productSliderFactory
     * @param CollectionFactory $collectionFactory
     * @param Visibility $visibility
     * @param CatalogConfig $catalogConfig
     * @param array $data
     */
    function __construct(
        ReadExtensions $readExtensions,
        JoinProcessorInterface $joinProcessor,
        NewWidgetFactory $newWidgetFactory,
        ProductSliderFactory $productSliderFactory,
        CollectionFactory $collectionFactory,
        Visibility $visibility,
        CatalogConfig $catalogConfig,
        array $data
    )
    {
        $this->_newWidgetFactory = $newWidgetFactory;
        $this->_productSliderFactory = $productSliderFactory;
        $this->_productCollectionFactory = $collectionFactory;
        $this->_catalogProductVisibility = $visibility;
        $this->_catalogConfig = $catalogConfig;
        parent::__construct($readExtensions, $joinProcessor, $data);
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $block = $this->_newWidgetFactory->create(['data' => $this->getData()]);
        $collection = $this->createCollection($block);
        $this->processCollection($collection);
        $collection->addAttributeToSelect('*');

        return $this->_productSliderFactory->create()
            ->setTitle($block->getData('title'))
            ->setShowMoreUrl($block->getData('show_more_url'))
            ->setDescription($block->getData('description'))
            ->setItems($collection->getItems())
            ->setTotalCount($collection->getSize());
    }

    /**
     * Create collection
     *
     * @param $block
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function createCollection($block)
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());

        $collection
            ->addFieldToFilter('created_at', ['gteq' => (new \DateTime())->modify('-500 days')->format('Y-m-d H:i:s')])
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addAttributeToFilter('status', Status::STATUS_ENABLED)
            ->addTaxPercents()
            ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
            ->addUrlRewrite()
            ->addStoreFilter()
            ->addAttributeToSort('created_at', 'desc');

        $collection->getSelect()->limitPage($block->getData('page') ?: 1, $block->getProductCount());

        return $collection;
    }
}
