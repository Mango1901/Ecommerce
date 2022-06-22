<?php

namespace Magenest\AffiliateOpt\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Account search result interface.
 * @api
 */
interface AccountSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \Magenest\AffiliateOpt\Api\Data\AccountInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \Magenest\AffiliateOpt\Api\Data\AccountInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items = null);
}
