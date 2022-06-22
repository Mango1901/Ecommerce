<?php

namespace Magenest\RewardPoints\Plugin;

use Magenest\RewardPoints\Api\Data\MembershipInterface;
use Magenest\RewardPoints\Helper\MembershipAction;
use Magenest\RewardPoints\Helper\MembershipData;
use Magenest\RewardPoints\Model\Account;
use Magenest\RewardPoints\Model\AccountFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class ConfigProviderPlugin
 *
 * @package Magenest\RewardPoints\Plugin
 */
class ConfigProviderPlugin
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var AccountFactory
     */
    protected $_accountFactory;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    private $helper;

    /**
     * @var MembershipData
     */
    protected $_membershipData;

    /**
     * ConfigProviderPlugin constructor.
     *
     * @param MembershipData $membershipDataHelper
     * @param CheckoutSession $checkoutSession
     * @param \Magenest\RewardPoints\Helper\Data $helper
     * @param AccountFactory $accountFactory
     * @param CustomerSession $customerSession
     */
    public function __construct(
        MembershipData $membershipData,
        CheckoutSession $checkoutSession,
        \Magenest\RewardPoints\Helper\Data $helper,
        AccountFactory $accountFactory,
        CustomerSession $customerSession
    ) {
        $this->_accountFactory = $accountFactory;
        $this->helper          = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->_membershipData = $membershipData;
    }

    /**
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param array $result
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, array $result)
    {
        if ($this->helper->getEnableModule()) {
            $customerId = $this->customerSession->getCustomerId();
            $customerTier = $this->_membershipData->getCustomerTier($customerId);
            $quote = $this->checkoutSession->getQuote();
            if ($customerTier->getId()) {
                $result['isIncludeTierReward'] = $customerTier->getData(MembershipInterface::ADDITIONAL_EARNING_POINT) == 1;
                $result['tierReward'] = $this->helper->getMembershipReward($quote);
            }

            $result['isRewardPointEnable']      = true;
            $points                             = $this->helper->getPointsEarnIncludeMembershipReward($quote);
            $result['checkoutRewardPointsEarn'] = $points;
            $result['checkoutRewardPointsLabel'] = $this->helper->getPointUnit();
            $rewardPoint                        = intval($quote->getData('reward_point'));
            if ($rewardPoint > 0) {
                $result['checkoutRewardsPointsSpend'] = $rewardPoint;
            } else {
                $result['checkoutRewardsPointsSpend'] = 0;
            }
            $result['earnPointWithAppliedPoints']   = $this->helper->getCanEarnPointWithAppliedPoints();
            $result['earnPointWithAppliedDiscount'] = $this->helper->getCanEarnPointWithAppliedDiscount();
            $currentPoint           = $this->getAccount($customerId)->getPointsCurrent();
            $configPoint            = $this->helper->getRewardBaseAmount(null);
            $result['currency']     = $this->checkoutSession->getQuote()->getBaseCurrencyCode();
            $result['configPoint']  = floatval($configPoint);
            $result['currentPoint'] = floatval($currentPoint);
        } else {
            $result['isRewardPointEnable'] = false;
        }

        return $result;
    }

    /**
     * @param $customerId
     * @return \Magento\Framework\DataObject
     */
    public function getAccount($customerId)
    {
        $account = $this->_accountFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->getFirstItem();

        return $account;
    }
}
