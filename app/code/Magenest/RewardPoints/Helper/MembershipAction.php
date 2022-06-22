<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 05/11/2020 14:41
 */

namespace Magenest\RewardPoints\Helper;

use Magenest\RewardPoints\Model\ResourceModel\Membership;
use Magenest\RewardPoints\Model\ResourceModel\Membership\CollectionFactory as MembershipGroupCollection;
use Magenest\RewardPoints\Model\ResourceModel\MembershipCustomer\CollectionFactory as MembershipCustomerCollection;
use Magenest\RewardPoints\Api\Data\AccountInterface;
use Magenest\RewardPoints\Api\Data\MembershipCustomerInterface;
use Magenest\RewardPoints\Api\Data\MembershipInterface;
use Magenest\RewardPoints\Model\Account;
use Magenest\RewardPoints\Model\ResourceModel\Account\CollectionFactory as AccountCollection;
use Magenest\RewardPoints\Model\ResourceModel\Membership\Collection;
use Magenest\RewardPoints\Model\MembershipCustomerFactory as MembershipCustomer;
use Magenest\RewardPoints\Model\ResourceModel\MembershipCustomer as MembershipCustomerResource;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;

class MembershipAction extends AbstractHelper
{
    const EMAIL_LEVEL_UP_ID = 'rewardpoints_email_rankup';

    const PATH_SEND_EMAIL_LEVEL_UP = 'rewardpoints/membership/reward_points_membership_email';
    /**
     * @var MembershipGroupCollection
     */
    protected $_groupCollection;

    /**
     * @var Account
     */
    protected $_accountInstance;

    /**
     * @var int
     */
    private $customerId;

    /**
     * @var AccountCollection
     */
    protected $_accountCollection;

    /**
     * @var MembershipCustomer
     */
    protected $_membershipCustomer;

    /**
     * @var MembershipCustomerResource
     */
    protected $_membershipCustomerResource;

    /**
     * @var Email
     */
    protected $_emailHelper;

    /**
     * @var CustomerRepository
     */
    protected $_customerRepository;

    /**
     * @var ImageHelper
     */
    protected $_imageHelper;

    /**
     * @var MembershipCustomerCollection
     */
    protected $_membershipCustomerCollection;

    /**
     * @var Membership
     */
    protected $_groupResource;

    /**
     * MemberAction constructor.
     * @param ImageHelper $imageHelper
     * @param CustomerRepository $customerRepository
     * @param Email $emailHelper
     * @param MembershipCustomerResource $membershipCustomerResource
     * @param MembershipCustomer $membershipCustomer
     * @param AccountCollection $accountCollection
     * @param MembershipGroupCollection $groupCollection
     * @param Membership $groupResource
     * @param MembershipCustomerCollection $membershipCustomerCollection
     * @param Context $context
     */
    public function __construct(
        ImageHelper $imageHelper,
        CustomerRepository $customerRepository,
        Email $emailHelper,
        MembershipCustomerResource $membershipCustomerResource,
        MembershipCustomer $membershipCustomer,
        AccountCollection $accountCollection,
        MembershipGroupCollection $groupCollection,
        Membership $groupResource,
        MembershipCustomerCollection $membershipCustomerCollection,
        Context $context
    ) {
        parent::__construct($context);
        $this->_groupResource = $groupResource;
        $this->_groupCollection = $groupCollection;
        $this->_membershipCustomerCollection = $membershipCustomerCollection;
        $this->_accountCollection = $accountCollection;
        $this->_membershipCustomer = $membershipCustomer;
        $this->_membershipCustomerResource = $membershipCustomerResource;
        $this->_emailHelper = $emailHelper;
        $this->_customerRepository = $customerRepository;
        $this->_imageHelper = $imageHelper;
    }

    /**
     * @return $this
     */
    public function upgradeTier()
    {
        try {
            // get the group that customer reach
            $tier = $this->getReachTier();
            if ($tier && $tier->getId()) {
                // update tier
                $membershipCustomer = $this->_membershipCustomer->create();
                $this->_membershipCustomerResource->load($membershipCustomer, $this->getCustomerId(), MembershipCustomerInterface::CUSTOMER_ID);
                $membershipCustomer->addData([
                    MembershipCustomerInterface::MEMBERSHIP_ID => $tier->getData(MembershipInterface::ENTITY_ID),
                    MembershipCustomerInterface::CUSTOMER_ID => $this->getCustomerId()
                ]);
                $this->_membershipCustomerResource->save($membershipCustomer);
                $this->sendEmailChangeTier($tier);
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return $this;
    }

    /**
     * @param $newTier
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendEmailChangeTier($newTier)
    {
        if ($this->scopeConfig->isSetFlag($this::PATH_SEND_EMAIL_LEVEL_UP)) {
            $emailData = $this->prepareLevelUpEmail($newTier);
            $this->_emailHelper->sendCustomEmail(self::EMAIL_LEVEL_UP_ID, $emailData, $this->_emailHelper->getSender(), ['name' => $emailData['name'], 'email' => $emailData['email']]);
        }
    }

    /**
     * @param int $originalPoints
     * @throws LocalizedException
     */
    public function applyAdditionalEarningPoints($originalPoints = 0)
    {
        $additionPoint = 0;
        if ($originalPoints) {
            $earningPoints = $this->getAdditionalEarningPoints();

            switch ($earningPoints['type']) {
                case MembershipInterface::ADDED_VALUE_TYPE_FIXED:
                    $additionPoint += $earningPoints['value'];
                    break;

                case MembershipInterface::ADDED_VALUE_TYPE_PERCENT:
                    $additionPoint += $originalPoints * $earningPoints['value'] / 100;
                    break;
            }
        }

        return $additionPoint;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    protected function getAdditionalEarningPoints()
    {
        $membershipTier = $this->getTierByCustomerId();

        $defaultAdditionalEarningPoints = [
            'type' => 0,
            'value' => 0
        ];

        if ($membershipTier && $membershipTier->getData(MembershipInterface::ADDITIONAL_EARNING_POINT)) {
            $defaultAdditionalEarningPoints = [
                'type' => $membershipTier->getData(MembershipInterface::ADDED_VALUE_TYPE),
                'value' => $membershipTier->getData(MembershipInterface::ADDED_VALUE_AMOUNT)
            ];
        }

        return $defaultAdditionalEarningPoints;
    }

    /**
     * get tier that the customer reach
     *
     * @return false|\Magento\Framework\DataObject
     * @throws LocalizedException
     */
    public function getReachTier()
    {
        $currentTier = $this->getTierByCustomerId();

        $groupCollection = $this->_groupCollection->create()
            ->addFieldToFilter(MembershipInterface::GROUP_STATUS, MembershipInterface::GROUP_STATUS_ENABLE);
        if ($currentTier->getId()) {
            $groupCollection = $groupCollection->addFieldToFilter(MembershipInterface::SORT_ORDER, ['lt' => $currentTier->getData(MembershipInterface::SORT_ORDER)]);
        }

        $conditions = $groupCollection->addOrder(MembershipInterface::SORT_ORDER, Collection::SORT_ORDER_DESC)->getItems();

        $bestCondition = false;
        foreach ($conditions as $condition) {
            switch ($condition->getData(MembershipInterface::GROUP_CONDITION_TYPE)) {
                case MembershipInterface::GROUP_CONDITION_TYPE_BY_POINT_NUMBER:
                    if ($this->getAccount()->getData(AccountInterface::POINTS_TOTAL) >= $condition->getData(MembershipInterface::GROUP_CONDITION_VALUE)) {
                        $bestCondition = $condition;
                    }
                    break;
                case MembershipInterface::GROUP_CONDITION_TYPE_BY_SPEND_POINT:
                    if ($this->getAccount()->getData(AccountInterface::POINTS_SPENT) >= $condition->getData(MembershipInterface::GROUP_CONDITION_VALUE)) {
                        $bestCondition = $condition;
                    }
                    break;
            }
        }
        return $bestCondition;
    }

    /**
     * @param $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * @return int
     * @throws LocalizedException
     */
    protected function getCustomerId()
    {
        if (empty($this->customerId)) {
            throw new LocalizedException(__('Something went wrong!'));
        }

        return $this->customerId;
    }

    /**
     * @return Account
     * @throws LocalizedException
     */
    protected function getAccount()
    {
        if (empty($this->_accountInstance)) {
            $this->_accountInstance = $this->_accountCollection->create()
                ->addFieldToFilter(AccountInterface::CUSTOMER_ID, $this->getCustomerId())->getFirstItem();
        }

        return $this->_accountInstance;
    }

    public function getTierByCustomerId()
    {
        if (!empty($this->getCustomerId())) {
            return $this->_membershipCustomerCollection->create()->joinMembership()
                ->addFieldToFilter(MembershipCustomerInterface::CUSTOMER_ID, $this->getCustomerId())
                ->addFieldToFilter('membership.' . MembershipInterface::GROUP_STATUS, MembershipInterface::GROUP_STATUS_ENABLE)
                ->getFirstItem();
        }

        return false;
    }

    /**
     * @param $tier
     * @return array
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function prepareLevelUpEmail($tier)
    {
        $customer = $this->_customerRepository->getById($this->getCustomerId());

        return [
            'email' => $customer->getEmail(),
            'name' => $customer->getLastname() . ' ' . $customer->getLastname(),
            'tier_name' => $tier->getData(MembershipInterface::GROUP_NAME),
            'tier_logo' => $this->_imageHelper->getRewardPointsViewFileUrl($tier->getData(MembershipInterface::TIER_LOGO)),
            'current_point' => $this->getAccount()->getData(AccountInterface::POINTS_CURRENT),
            'account_dashboard' => $this->_getUrl('customer/account/')
        ];
    }

    /**
     * @param array $membershipIds
     * @return int
     * @throws \Exception
     */
    public function deleteMembershipGroup($membershipIds = [])
    {
        if (!is_array($membershipIds)) {
            $membershipIds = [$membershipIds];
        }

        $membershipDeleted = 0;
        $collection = $this->_groupCollection->create()->addFieldToFilter(MembershipInterface::ENTITY_ID, ['in' => $membershipIds]);
        /**
         * @var \Magenest\RewardPoints\Model\Membership $membership
         */
        foreach ($collection->getItems() as $membership) {
            $this->_groupResource->delete($membership);
            $this->deleteMembershipCustomerByGroupId($membership->getId());
            $membershipDeleted++;
        }

        return $membershipDeleted;
    }

    /**
     * @param array $groupIds
     * @throws \Exception
     */
    public function deleteMembershipCustomerByGroupId($groupIds = [])
    {
        if (!is_array($groupIds)) {
            $groupIds = [$groupIds];
        }
        $collection = $this->_membershipCustomerCollection->create()->addFieldToFilter(MembershipCustomerInterface::MEMBERSHIP_ID, ['in' => $groupIds]);

        /**
         * @var \Magenest\RewardPoints\Model\MembershipCustomer $membershipCustomer
         */
        foreach ($collection->getItems() as $membershipCustomer) {
            $this->_membershipCustomerResource->delete($membershipCustomer);
        }
    }
}
