<?php
namespace Magenest\MobileApi\Api\Data;

use Magento\Review\Model\ResourceModel\Review\Product\Collection as ReviewCollection;
use Magento\Review\Model\ResourceModel\Review\Product\CollectionFactory as ReviewCollectionFactory;
use Magenest\MobileApi\Model\Converter\Review\ToDataModel as ReviewConverter;

class ProductReviewsInterface
{
    public function getProductReviewsById(int $productId)
    {

    }
}
