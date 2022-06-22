<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Catalog\Widget;

use Magento\CatalogWidget\Block\Product\ProductsListFactory;
use Magenest\MobileApi\Model\Catalog\WidgetAbstract;
use Magento\Framework\EntityManager\Operation\Read\ReadExtensions;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magenest\MobileApi\Model\Catalog\Widget\ProductSliderFactory;

/**
 * Class Product
 * @package Magenest\MobileApi\Model\Catalog\Widget
 */
class Product extends WidgetAbstract
{
    /**
     * @var ProductsListFactory
     */
    protected $_productListFactory;

    /**
     * @var ProductSliderFactory
     */
    protected $_productSliderFactory;

    /**
     * Constructor.
     *
     * @param ReadExtensions $readExtensions
     * @param JoinProcessorInterface $joinProcessor
     * @param ProductsListFactory $productListFactory
     * @param ProductSliderFactory $productSliderFactory
     * @param array $data
     */
    function __construct(
        ReadExtensions $readExtensions,
        JoinProcessorInterface $joinProcessor,
        ProductsListFactory $productListFactory,
        ProductSliderFactory $productSliderFactory,
        array $data
    )
    {
        $this->_productListFactory = $productListFactory;
        $this->_productSliderFactory = $productSliderFactory;
        parent::__construct($readExtensions, $joinProcessor, $data);
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $block = $this->_productListFactory->create(['data' => $this->getData()]);
        $collection = $block->createCollection();
        $collection->setCurPage($block->getData('page') ?: 1);
        $this->processCollection($collection);

        return $this->_productSliderFactory->create()
            ->setTitle($block->getTitle())
            ->setDescription($block->getData('description'))
            ->setShowMoreUrl($block->getData('show_more_url'))
            ->setCategoryId($block->getData('category_id'))
            ->setBanners($block->getData('banners'))
            ->setItems($collection->getItems())
            ->setTotalCount($collection->getSize())
            ->setType('product_slider');
    }
}