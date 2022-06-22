<?php

namespace Magenest\AffiliateOpt\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magenest\AffiliateOpt\Api\Data\AccountInterface;

/**
 * Interface AccountRepositoryInterface
 * @api
 */
interface AccountRepositoryInterface
{
    /**
     * @param int $id The account ID.
     *
     * @return \Magenest\AffiliateOpt\Api\Data\AccountInterface Account.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * Lists Account that match specified search criteria.
     *
     * This call returns an array of objects, but detailed information about each object’s attributes might not be
     * included.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria The search criteria.
     *
     * @return \Magenest\AffiliateOpt\Api\Data\AccountSearchResultInterface Account search result interface.
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param string $email
     *
     * @return \Magenest\AffiliateOpt\Api\Data\AccountInterface Account
     */
    public function getAccountByEmail($email);

    /**
     * @return int
     */
    public function count();

    /**
     * @param int $id account id
     *
     * @return bool true on success
     */
    public function deleteById($id);

    /**
     * @param int $id account id
     * @param int $value
     *
     * @return bool true on success
     */
    public function changeStatus($id, $value);

    /**
     * @param int $id account id
     *
     * @return \Magenest\AffiliateOpt\Api\Data\AccountSearchResultInterface Account search result interface.
     */
    public function getChildAccount($id);

    /**
     * @param string $email
     *
     * @return \Magenest\AffiliateOpt\Api\Data\AccountSearchResultInterface Account search result interface.
     */
    public function getChildAccountByEmail($email);

    /**
     * @param int $id
     *
     * @return \Magenest\AffiliateOpt\Api\Data\CampaignSearchResultInterface
     */
    public function getCampaignById($id);

    /**
     * @param string $email
     *
     * @return \Magenest\AffiliateOpt\Api\Data\CampaignSearchResultInterface
     */
    public function getCampaignByEmail($email);

    /**
     * Required(customer_id, group_id, status)
     *
     * @param \Magenest\AffiliateOpt\Api\Data\AccountInterface $data
     *
     * @return int Account id created
     * @throws \Magento\Framework\Exception\LocalizedException;
     */
    public function save(AccountInterface $data);
}
