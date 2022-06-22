<?php

namespace Magenest\AffiliateOpt\Model;

use Exception;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magenest\Affiliate\Model\Account\Status;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\GroupFactory;
use Magenest\AffiliateOpt\Api\AccountRepositoryInterface;
use Magenest\AffiliateOpt\Api\Data\AccountInterface;
use Magenest\AffiliateOpt\Api\Data\AccountSearchResultInterface;
use Magenest\AffiliateOpt\Api\Data\AccountSearchResultInterfaceFactory as SearchResultFactory;
use Magenest\AffiliateOpt\Api\Data\CampaignSearchResultInterfaceFactory;
use Magenest\AffiliateOpt\Helper\Data;
use Magenest\AffiliateOpt\Model\AccountFactory as AccountAPIFactory;

/**
 * Class AccountRepository
 *
 * @package Magenest\AffiliateOpt\Model
 */
class AccountRepository implements AccountRepositoryInterface
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var SearchResultFactory
     */
    protected $searchResultFactory = null;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var bool
     */
    protected $isStandard = false;

    /**
     * @var AccountFactory
     */
    protected $accountFactory;

    /**
     * @var \Magenest\AffiliateOpt\Model\AccountFactory
     */
    protected $accountAPIFactory;

    /**
     * @var Status
     */
    protected $status;

    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var GroupFactory
     */
    protected $group;

    /**
     * @var CampaignSearchResultInterfaceFactory
     */
    protected $campaignSearchResultInterfaceFactory;

    /**
     * AccountRepository constructor.
     *
     * @param Data                                              $helperData
     * @param SearchResultFactory                               $searchResultFactory
     * @param SearchCriteriaBuilder                             $searchCriteriaBuilder
     * @param FilterBuilder                                     $filterBuilder
     * @param AccountFactory                                    $accountFactory
     * @param \Magenest\AffiliateOpt\Model\AccountFactory $accountAPIFactory
     * @param Status                                            $status
     * @param CustomerRegistry                                  $customerRegistry
     * @param GroupFactory                                      $group
     * @param CampaignSearchResultInterfaceFactory              $campaignSearchResultInterfaceFactory
     */
    public function __construct(
        Data $helperData,
        SearchResultFactory $searchResultFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        AccountFactory $accountFactory,
        AccountAPIFactory $accountAPIFactory,
        Status $status,
        CustomerRegistry $customerRegistry,
        GroupFactory $group,
        CampaignSearchResultInterfaceFactory $campaignSearchResultInterfaceFactory
    ) {
        $this->helperData                           = $helperData;
        $this->searchResultFactory                  = $searchResultFactory;
        $this->searchCriteriaBuilder                = $searchCriteriaBuilder;
        $this->filterBuilder                        = $filterBuilder;
        $this->accountFactory                       = $accountFactory;
        $this->accountAPIFactory                    = $accountAPIFactory;
        $this->status                               = $status;
        $this->customerRegistry                     = $customerRegistry;
        $this->group                                = $group;
        $this->campaignSearchResultInterfaceFactory = $campaignSearchResultInterfaceFactory;
    }

    /**
     * Find entities by criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return AccountSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResult = $this->searchResultFactory->create();

        return $this->helperData->processGetList($searchCriteria, $searchResult);
    }

    /**
     * {@inheritDoc}
     */
    public function getAccountByEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InputException(__('Invalid email.'));
        }

        $account = $this->searchResultFactory->create();
        $this->joinCustomerByEmail($email, $account);

        if ($account->getTotalCount() > 0) {
            return $account->getItems()[0];
        } else {
            throw new NoSuchEntityException(__('Requested email doesn\'t exist'));
        }
    }

    /**
     * @param $email
     * @param $account
     */
    public function joinCustomerByEmail($email, $account)
    {
        $account->getSelect()->join(
            ['customer' => $account->getTable('customer_entity')],
            'main_table.customer_id = customer.entity_id',
            ['email']
        )->where('customer.email = ?', $email);
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        if (!$id) {
            throw new InputException(__('Account id required'));
        }

        if ($this->isStandard) {
            $account = $this->getAccount()->load($id);
        } else {
            $account = $this->accountAPIFactory->create()->load($id);
        }

        if (!$account->getId()) {
            throw new NoSuchEntityException(__('Requested entity doesn\'t exist'));
        }

        return $account;
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return $this->searchResultFactory->create()->getTotalCount();
    }

    /**
     * {@inheritDoc}
     */
    public function deleteById($id)
    {
        try {
            $this->get($id)->delete();
        } catch (Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the account: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function changeStatus($id, $value)
    {
        if (!$value) {
            throw new InputException(__('Id required'));
        }

        $this->validateStatus($value);

        try {
            $this->get($id)->setStatus($value)->save();
        } catch (Exception $e) {
            throw new CouldNotSaveException((__('Could not change status the account: %1', $e->getMessage())));
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getChildAccount($id)
    {
        $accounts = $this->searchResultFactory->create();
        $accounts->getSelect()->where("tree LIKE '$id/%' OR tree LIKE '%/$id/%'");

        return $accounts;
    }

    /**
     * {@inheritDoc}
     */
    public function getChildAccountByEmail($email)
    {
        $currentAccount = $this->getAccountByEmail($email);

        return $this->getChildAccount($currentAccount->getAccountId());
    }

    /**
     * {@inheritDoc}
     */
    public function getCampaignById($id)
    {
        $account = $this->get($id);

        return $this->getCampaignsByGroup($account->getGroupId());
    }

    /**
     * {@inheritDoc}
     */
    public function getCampaignByEmail($email)
    {
        $account = $this->getAccountByEmail($email);

        return $this->getCampaignsByGroup($account->getGroupId());
    }

    /**
     * @param $group
     *
     * @return mixed
     */
    public function getCampaignsByGroup($group)
    {
        $campaigns = $this->campaignSearchResultInterfaceFactory->create();
        $campaigns->getSelect()->where("affiliate_group_ids LIKE '$group,%'
                                OR affiliate_group_ids LIKE '%,$group,%'
                                OR affiliate_group_ids LIKE '%,$group%'");

        return $campaigns;
    }

    /**
     * {@inheritDoc}
     */
    public function save(AccountInterface $data)
    {
        if (empty($data->getCustomerId())) {
            throw new InputException(__('Customer id required'));
        }

        if (empty($data->getGroupId())) {
            throw new InputException(__('Affiliate group required'));
        }

        $group = $this->group->create()->load($data->getGroupId());
        if (!$group->getId()) {
            throw new NoSuchEntityException(__('Affiliate group doesn\'t exist'));
        }

        if (empty($data->getStatus())) {
            throw new InputException(__('Status required'));
        }
        $this->validateStatus($data->getStatus());

        $customer = $this->customerRegistry->retrieve($data->getCustomerId());
        if ($this->getAccount()->load($customer->getId(), 'customer_id')->getId()) {
            throw new InputException(__(
                'The customer "%1" has registed as an affiliate already',
                $customer->getEmail()
            ));
        }
        $affiliateData = [
            'customer_id'        => $customer->getId(),
            'customer_email'     => $customer->getEmail(),
            'group'              => $data->getGroupId(),
            'status'             => $data->getStatus(),
            'email_notification' => $data->getEmailNotification()
        ];

        if ($data->getParent()) {
            $parent                   = $this->getAccount()->load($data->getParent());
            $parentEmail              = $this->customerRegistry->retrieve($parent->getCustomerId())->getEmail();
            $affiliateData['parent']  = $parent->getId();
            $affiliateData['$parent'] = $parentEmail;
        }

        try {
            $account = $this->getAccount()->setData($affiliateData)->save();
        } catch (Exception $e) {
            throw new CouldNotSaveException((__('Could not save the affiliate: %1', $e->getMessage())));
        }

        return $account->getId();
    }

    /**
     * @return mixed
     */
    public function getAccount()
    {
        return $this->accountFactory->create();
    }

    /**
     * @param $value
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function validateStatus($value)
    {
        $statusAvailable = $this->status->toOptionHash();
        if (!isset($statusAvailable[$value])) {
            throw new NoSuchEntityException(__('Requested status doesn\'t exist'));
        }

        return $value;
    }
}
