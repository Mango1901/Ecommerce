<?php
namespace Magenest\MobileApi\Api;

interface ProductReviewsInterface
{
    /**
     * Get product reviews by productId
     *
     * @param int $storeId
     * @param int $productId
     * @return \Magenest\MobileApi\Api\Data\ReviewInterface[]
     */
    public function getProductReviewsById(int $storeId, int $productId);
}
