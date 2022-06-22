<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 * NOTICE OF LICENSE
 *
 * @category Magenest
 */

namespace Magenest\RewardPoints\Model\Api;

use Magenest\RewardPoints\Api\ProcessPointInterface;
use Magenest\RewardPoints\Helper\AddPoint;
use Magenest\RewardPoints\Helper\Data;
use Magenest\RewardPoints\Model\ResourceModel\Account;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class ProcessPoint
 * @package Magenest\RewardPoints\Model\Api
 */
class ProcessPoint implements ProcessPointInterface
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var CurrentCustomer
     */
    protected $_currentCustomer;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var AddPoint
     */
    protected $addPoint;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var \Magenest\RewardPoints\Model\AccountFactory
     */
    protected $_accountFactory;

    protected $_accountResource;

    /**
     * Checkout constructor.
     * @param Account $accountResource
     * @param \Magenest\RewardPoints\Model\AccountFactory $accountFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param CurrentCustomer $currentCustomer
     * @param Session $checkoutSession
     * @param Data $helper
     * @param ResultFactory $resultFactory
     * @param AddPoint $addPoint
     * @param Cart $cart
     */
    public function __construct(
        Account $accountResource,
        \Magenest\RewardPoints\Model\AccountFactory $accountFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        CurrentCustomer $currentCustomer,
        Session $checkoutSession,
        Data $helper,
        ResultFactory $resultFactory,
        AddPoint $addPoint,
        Cart $cart
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->checkoutSession  = $checkoutSession;
        $this->helper           = $helper;
        $this->_currentCustomer = $currentCustomer;
        $this->resultFactory    = $resultFactory;
        $this->addPoint         = $addPoint;
        $this->cart             = $cart;
        $this->_accountFactory  = $accountFactory;
        $this->_accountResource = $accountResource;
    }

    /**
     * @return false|float|string
     * @throws \Exception
     */
    public function calculateMaxAppliedPoint()
    {
        $quote = $this->cart->getQuote();
        $baseSubtotal = $quote->getData('base_subtotal');
        $couponCode = $quote->getData('coupon_code');
        if ($couponCode) {
            $subtotalWithDiscount = $quote->getData('subtotal_with_discount');
        }
        //Get data in config
        $spendingConfigurationEnable = $this->helper->getSpendingConfigurationEnable();
        $percentMaxOrder = $this->helper->getPercentMaxOrder();
        $spendingPoint = $this->helper->getSpendingPoint();

        //Where customers use config in spending
        if ($spendingConfigurationEnable) {
            if ($spendingPoint == Data::SPENDING_POINT_PERCENTAGE_VALUE) {
                if ($couponCode && isset($subtotalWithDiscount)) {
                    $maxAppliedOrderValueCanUse = $subtotalWithDiscount * $percentMaxOrder / 100;
                } else {
                    $maxAppliedOrderValueCanUse = $baseSubtotal * $percentMaxOrder / 100;
                }
                $maxAppliedPoint = $this->helper->convertValueToPoint($maxAppliedOrderValueCanUse);
            } else {
                $maximumPoint = $this->helper->getMaximumPoint();
                $maxAppliedOrderValueCanUse = $subtotalWithDiscount ?? $baseSubtotal;
                $maxAppliedPoint = $this->helper->convertValueToPoint($maxAppliedOrderValueCanUse);
                if ($maxAppliedPoint > $maximumPoint) {
                    $maxAppliedPoint = $maximumPoint;
                }
            }
        } else {
            $maxAppliedOrderValueCanUse = $subtotalWithDiscount ?? $baseSubtotal;
            $maxAppliedPoint = $this->helper->convertValueToPoint($maxAppliedOrderValueCanUse);
        }

        $currentPoint = $this->getCurrentPoint();
        if ($currentPoint < $maxAppliedPoint) {
            $maxAppliedPoint = $currentPoint;
        }

        return $maxAppliedPoint;
    }

    /**
     * @return int|string
     */
    public function getCurrentPoint()
    {
        $account = $this->_accountFactory->create();
        $this->_accountResource->load($account, $this->_currentCustomer->getCustomerId(), 'customer_id');
        return $account->getPointsCurrent();
    }

    /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function loadPoint()
    {
        $quote = $this->checkoutSession->getQuote();
        $spendingConfig = $this->helper->getSpendingConfigurationEnable();
        if ($spendingConfig) {
            $this->addPoint->loadPoint($quote);
        }
        //totalReward point from the account
        //Reward points amount that could apply for current cart
        $reward_point = $this->checkoutSession->getQuote()->getData('reward_point');
        $result['rewardPointAmount'] = floatval($reward_point);
        $pointEarned = $this->helper->getPointsEarnIncludeMembershipReward($this->checkoutSession->getQuote());
        $result['pointEarned'] = $pointEarned;
        $result['pointLabel'] = $this->helper->getPointUnit();

        return $this->jsonHelper->jsonEncode($result);
    }

    /**
     * @param int $point
     * @return string|null
     */
    public function addPoint($point)
    {
        $this->addPoint->addPoint($point);
        $result = true;
        return $this->jsonHelper->jsonEncode($result);
    }

    /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function cancelPoint()
    {
        $quote = $this->checkoutSession->getQuote();
        $quote->setData('reward_point', 0)->save();
        $quote->setData('reward_amount', 0)->save();
        $quote->setData('base_reward_amount', 0)->save();
        $this->cart->getQuote()->collectTotals()->save();
        $result = true;
        return $this->jsonHelper->jsonEncode($result);
    }
}
