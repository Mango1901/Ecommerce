<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Model\Plugin\ProductLink;

use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Helper\Image as CatalogImageHelper;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product;
use Magento\Review\Model\ReviewFactory;

/**
 * Class Converter
 * @package Magenest\MobileApi\Model\Plugin\ProductLink
 */
class Converter
{
    /**
     * @var ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * Constructor.
     *
     * @param ReviewFactory $reviewFactory
     */
    function __construct(
        ReviewFactory $reviewFactory
    )
    {
        $this->_reviewFactory = $reviewFactory;
    }

    /**
     * After convert
     *
     * @param $subject
     * @param array $result
     * @param Product $product
     * @return array
     */
    public function afterConvert($subject, $result, Product $product)
    {
        $product->getResource()->load($product, $product->getId(), ['name']);
        $this->_reviewFactory->create()->getEntitySummary($product);

        $result['custom_attributes'][] = ['attribute_code' => 'id', 'value' => $product->getId()];
        $result['custom_attributes'][] = ['attribute_code' => 'price', 'value' => $product->getPrice()];
        $result['custom_attributes'][] = ['attribute_code' => 'final_price', 'value' => $product->getFinalPrice()];
        $result['custom_attributes'][] = ['attribute_code' => 'name', 'value' => $product->getName()];
        $result['custom_attributes'][] = ['attribute_code' => 'review_counts', 'value' => $product->getRatingSummary()->getReviewsCount()];
        $result['custom_attributes'][] = ['attribute_code' => 'rating', 'value' => $product->getRatingSummary()->getRatingSummary()];
        $result['custom_attributes'][] = ['attribute_code' => 'image', 'value' => $product->getImage()];
        $result['custom_attributes'][] = ['attribute_code' => 'is_new', 'value' => (strtotime($product->getCreatedAt()) >= strtotime('-500 days'))];

        return $result;
    }
}