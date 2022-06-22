<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ProductLabel
 */

namespace Magenest\ProductLabel\Api\Data;

/**
 * Interface for label search results
 *
 * @api
 */
interface LabelSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Gets collection items.
     *
     * @return \Magenest\ProductLabel\Api\Data\LabelInterface[]
     */
    public function getItems();

    /**
     * Set collection items.
     *
     * @param \Magenest\ProductLabel\Api\Data\LabelInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
