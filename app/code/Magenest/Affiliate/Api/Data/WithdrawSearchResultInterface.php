<?php


namespace Magenest\Affiliate\Api\Data;

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
     * @return \Magenest\Affiliate\Api\Data\WithdrawInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \Magenest\Affiliate\Api\Data\WithdrawInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items = null);
}
