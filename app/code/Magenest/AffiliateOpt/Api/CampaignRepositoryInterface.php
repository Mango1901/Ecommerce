<?php

namespace Magenest\AffiliateOpt\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface CampaignRepositoryInterface
 * @api
 */
interface CampaignRepositoryInterface
{
    /**
     * Lists campaign that match specified search criteria.
     *
     * This call returns an array of objects, but detailed information about each object’s attributes might not be
     * included.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria The search criteria.
     *
     * @return \Magenest\AffiliateOpt\Api\Data\CampaignSearchResultInterface Campaign search result interface.
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $id campaign id
     *
     * @return \Magenest\AffiliateOpt\Api\Data\CampaignInterface Campaign
     */
    public function get($id);

    /**
     * @param int $id campaign id
     * @param int $value
     *
     * @return bool true on success
     */
    public function changeStatus($id, $value);

    /**
     * @param int $id campaign id
     *
     * @return bool true on success
     */
    public function deleteById($id);
}
