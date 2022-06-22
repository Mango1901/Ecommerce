<?php

namespace Magenest\AffiliateOpt\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magenest\AffiliateOpt\Api\Data\TransactionInterface;

/**
 * Interface TransactionRepositoryInterface
 * @api
 */
interface TransactionRepositoryInterface
{
    /**
     * Lists transaction that match specified search criteria.
     *
     * This call returns an array of objects, but detailed information about each object’s attributes might not be
     * included.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria The search criteria.
     *
     * @return \Magenest\AffiliateOpt\Api\Data\TransactionSearchResultInterface Transaction search result
     *     interface.
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $id Affiliate id
     *
     * @return \Magenest\AffiliateOpt\Api\Data\TransactionSearchResultInterface Transaction search result
     *     interface.
     */
    public function getTransactionByAffiliateId($id);

    /**
     * @param int $id Order id
     *
     * @return \Magenest\AffiliateOpt\Api\Data\TransactionSearchResultInterface Transaction search result
     *     interface.
     */
    public function getTransactionByOrderId($id);

    /**
     * Required(affiliate_id, amount)
     *
     * @param \Magenest\AffiliateOpt\Api\Data\TransactionInterface $data
     *
     * @return int Transaction id created
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(TransactionInterface $data);

    /**
     * @return int
     */
    public function count();

    /**
     * Cancels a specified transaction.
     *
     * @param int $id The transaction ID.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function cancel($id);

    /**
     * Completes a specified transaction.
     *
     * @param int $id The transaction ID.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function complete($id);

    /**
     * Required(affiliate_id, amount)
     *
     * @param \Magenest\AffiliateOpt\Api\Data\TransactionInterface $data
     * @param string $action
     * @return int Transaction id created
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createTransactionByAction(TransactionInterface $data, $action);
}
