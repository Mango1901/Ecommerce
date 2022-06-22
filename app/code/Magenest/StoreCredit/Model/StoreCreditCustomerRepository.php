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

namespace Magenest\StoreCredit\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magenest\StoreCredit\Api\Data\StoreCreditCustomerSearchResultInterface;
use Magenest\StoreCredit\Api\Data\StoreCreditCustomerSearchResultInterfaceFactory as SearchResultFactory;
use Magenest\StoreCredit\Api\StoreCreditCustomerRepositoryInterface;
use Magenest\StoreCredit\Helper\Account as HelperAccount;
use Magenest\StoreCredit\Helper\Data;
use Magenest\StoreCredit\Model\ResourceModel\Customer\Collection;

/**
 * Class StoreCreditCustomerRepository
 * @package Magenest\StoreCredit\Model
 */
class StoreCreditCustomerRepository implements StoreCreditCustomerRepositoryInterface
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var SearchResultFactory
     */
    protected $searchResultFactory;

    /**
     * @var CustomerFactory
     */
    protected $storeCreditCustomerFactory;

    /**
     * @var HelperAccount
     */
    protected $helperAccount;

    /**
     * StoreCreditCustomerRepository constructor.
     *
     * @param Data $helperData
     * @param SearchResultFactory $searchResultFactory
     * @param CustomerFactory $storeCreditCustomerFactory
     * @param HelperAccount $helperAccount
     */
    public function __construct(
        Data $helperData,
        SearchResultFactory $searchResultFactory,
        CustomerFactory $storeCreditCustomerFactory,
        HelperAccount $helperAccount
    ) {
        $this->helperData = $helperData;
        $this->searchResultFactory = $searchResultFactory;
        $this->storeCreditCustomerFactory = $storeCreditCustomerFactory;
        $this->helperAccount = $helperAccount;
    }

    /**
     * Find entities by criteria
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return StoreCreditCustomerSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null)
    {
        /** @var Collection $searchResult */
        $searchResult = $this->searchResultFactory->create();
        $storeCreditCustomerCollection = $this->helperData->processGetList($searchResult, $searchCriteria);
        foreach ($storeCreditCustomerCollection->getItems() as $item) {
            $item->setMpCreditBalance($this->helperAccount->getConvertAndFormatBalance(
                $item->getMpCreditBalance(),
                $item->getCustomerId()
            ));
        }

        return $storeCreditCustomerCollection;
    }

    /**
     * {@inheritDoc}
     */
    public function getAccountByCustomerId($customerId)
    {
        $storeCreditCustomer = $this->storeCreditCustomerFactory->create()->loadByCustomerId($customerId);
        $storeCreditCustomer->setMpCreditBalance($this->helperAccount->getConvertAndFormatBalance(
            $storeCreditCustomer->getMpCreditBalance(),
            $customerId
        ));

        return $storeCreditCustomer;
    }

    /**
     * {@inheritDoc}
     */
    public function updateNotification($customerId, $isReceiveNotification)
    {
        $data = [
            'mp_credit_notification' => $isReceiveNotification
        ];
        $customerModel = $this->storeCreditCustomerFactory->create();
        $customerModel->load($customerId);
        $customerModel->saveAttributeData($customerId, $data);

        return true;
    }
}
