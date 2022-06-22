<?php

namespace Magenest\AffiliateOpt\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magenest\AffiliateOpt\Api\Data\WithdrawInterface;
use Magenest\AffiliateOpt\Api\Data\WithdrawSearchResultInterface;

/**
 * Interface WithdrawRepositoryInterface
 * @api
 */
interface WithdrawRepositoryInterface
{
    /**
     * Lists withdraw that match specified search criteria.
     *
     * This call returns an array of objects, but detailed information about each object’s attributes might not be
     * included.
     *
     * @param SearchCriteriaInterface $searchCriteria The search criteria.
     *
     * @return WithdrawSearchResultInterface Withdraw search result interface.
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $id The withdraw ID.
     *
     * @return WithdrawInterface Affiliate.
     * @throws NoSuchEntityException
     */
    public function get($id);

    /**
     * @param int affiliateId The Affiliate ID.
     *
     * @return WithdrawSearchResultInterface Withdraw search result interface.
     * @throws NoSuchEntityException
     */
    public function getByAffiliateId($affiliateId);

    /**
     * @param int $id The withdraw ID.
     *
     * @return bool true on success
     * @throws NoSuchEntityException
     */
    public function approve($id);

    /**
     * @param int $id The withdraw ID.
     *
     * @return bool true on success
     * @throws NoSuchEntityException
     */
    public function cancel($id);

    /**
     * Required(account_id, amount, payment_method)
     * Paypal method required paypal_email field
     *
     * @param WithdrawInterface $data
     *
     * @return int Withdraw id created
     * @throws LocalizedException
     */
    public function save(WithdrawInterface $data);
}
