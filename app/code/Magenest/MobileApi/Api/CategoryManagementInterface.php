<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Api;

/**
 * Interface CategoryManagementInterface
 * @package Magenest\MobileApi\Api
 */
interface CategoryManagementInterface
{
    /**
     * Get products assigned to category
     *
     * @param int $categoryId
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     */
    public function getAssignedProducts($categoryId);
}
