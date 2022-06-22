<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api\Filter;

/**
 * Interface FilterManagementInterface
 * @package Magenest\MobileApi\Api\Filter
 */
interface FilterManagementInterface
{
    /**
     * Get category filter
     *
     * @param int $categoryId
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     */
    public function getCategoryFilters($categoryId);

    /**
     * Get search filter
     *
     * @param string $searchTerm
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     */
    public function getSearchFilters($searchTerm);
}