<?php

namespace Magenest\AffiliateOpt\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Withdraw search result interface.
 * @api
 */
interface WithdrawSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \Magenest\AffiliateOpt\Api\Data\WithdrawInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \Magenest\AffiliateOpt\Api\Data\WithdrawInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items = null);
}
