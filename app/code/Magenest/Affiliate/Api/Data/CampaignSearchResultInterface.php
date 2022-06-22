<?php


namespace Magenest\Affiliate\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Withdraw search result interface.
 * @api
 */
interface CampaignSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \Magenest\Affiliate\Api\Data\CampaignInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \Magenest\Affiliate\Api\Data\CampaignInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items = null);
}
