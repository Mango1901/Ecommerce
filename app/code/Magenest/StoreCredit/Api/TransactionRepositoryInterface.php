<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Api;

/**
 * Interface TransactionRepositoryInterface
 * @api
 */
interface TransactionRepositoryInterface
{
    /**
     * Lists Transaction that match specified search criteria.
     *
     * This call returns an array of objects, but detailed information about each object’s attributes might not be
     * included.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria The search criteria.
     *
     * @return \Magenest\StoreCredit\Api\Data\TransactionSearchResultInterface Transaction search result
     *     interface.
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null);

    /**
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria The search criteria.
     *
     * @return \Magenest\StoreCredit\Api\Data\TransactionSearchResultInterface
     */
    public function getTransactionByCustomerId(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null
    );

    /**
     * Required(customer_id, amount)
     *
     * @param \Magenest\StoreCredit\Api\Data\TransactionInterface $data
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create(\Magenest\StoreCredit\Api\Data\TransactionInterface $data);

    /**
     * Required(customer_id, amount)
     *
     * @param \Magenest\StoreCredit\Api\Data\TransactionInterface $data
     * @param $action
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createTransactionByAction(\Magenest\StoreCredit\Api\Data\TransactionInterface $data, $action);
}
