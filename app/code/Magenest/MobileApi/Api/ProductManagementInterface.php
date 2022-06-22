<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DataObject;
use Magenest\MobileApi\Api\Data\Catalog\Product\ReviewInterface;

/**
 * Interface ProductManagementInterface
 * @package Magenest\MobileApi\Api
 */
interface ProductManagementInterface
{
    /**
     * Get product list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Search products by term
     *
     * @param string $searchTerm
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     */
    public function search($searchTerm, SearchCriteriaInterface $searchCriteria);

    /**
     * Search suggest by term
     *
     * @param string $searchTerm
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     */
    public function searchSuggest($searchTerm);

    /**
     * Search popular
     *
     * @param int $storeId
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     */
    public function searchPopular($storeId = null);

    /**
     * Get viewed product
     *
     * @param int $customerId
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     */
    public function viewed($customerId);

    /**
     * Save view
     *
     * @param int $productId
     * @param \Magenest\MobileApi\Api\Data\Catalog\Product\ReviewInterface $review
     * @param int|null $customerId
     * @return bool
     */
    public function saveReview($productId, ReviewInterface $review, $customerId = null);

    /**
     * Get product links
     *
     * @param int $productId
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getLinks(int $productId);

    /**
     * Get product upsell
     *
     * @param int $productId
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getUpsell(int $productId);
}
