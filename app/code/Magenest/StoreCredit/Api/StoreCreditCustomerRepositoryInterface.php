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
 * Interface StoreCreditCustomerRepositoryInterface
 * @api
 */
interface StoreCreditCustomerRepositoryInterface
{
    /**
     * Lists Account that match specified search criteria.
     *
     * This call returns an array of objects, but detailed information about each object’s attributes might not be
     * included.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria The search criteria.
     *
     * @return \Magenest\StoreCredit\Api\Data\StoreCreditCustomerSearchResultInterface Account search result interface.
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null);

    /**
     * @param int $customerId
     *
     * @return \Magenest\StoreCredit\Api\Data\StoreCreditCustomerInterface
     */
    public function getAccountByCustomerId($customerId);

    /**
     * @param int $customerId
     * @param boolean $isReceiveNotification
     *
     * @return boolean
     */
    public function updateNotification($customerId, $isReceiveNotification);
}
