<?php


namespace Magenest\Affiliate\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Transaction search result interface.
 * @api
 */
interface TransactionSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \Magenest\Affiliate\Api\Data\TransactionInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \Magenest\Affiliate\Api\Data\TransactionInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items = null);
}
