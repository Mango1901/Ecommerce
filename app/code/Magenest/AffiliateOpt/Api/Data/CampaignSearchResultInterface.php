<?php

namespace Magenest\AffiliateOpt\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Campaign search result interface.
 * @api
 */
interface CampaignSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \Magenest\AffiliateOpt\Api\Data\CampaignInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \Magenest\AffiliateOpt\Api\Data\CampaignInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items = null);
}
