<?php

namespace Magenest\RewardPoints\Helper;

use Magento\Framework\App\Helper\Context;
use Magenest\RewardPoints\Model\AccountFactory;
use Magento\Customer\Helper\Session\CurrentCustomer;

/**
 * Class AddPoint
 * @package Magenest\RewardPoints\Helper
 */
class AddPoint extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var AccountFactory
     */
    protected $_accountFactory;

    /**
     * @var CurrentCustomer
     */
    protected $_currentCustomer;

    /**
     * AddPoint constructor.
     * @param Data $helper
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param AccountFactory $accountFactory
     * @param CurrentCustomer $currentCustomer
     * @param Context $context
     */
    public function __construct(
        \Magenest\RewardPoints\Helper\Data $helper,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Checkout\Model\Session $checkoutSession,
        AccountFactory $accountFactory,
        CurrentCustomer $currentCustomer,
        Context $context
    )
    {
        parent::__construct($context);
        $this->_helper            = $helper;
        $this->cart               = $cart;
        $this->checkoutSession    = $checkoutSession;
        $this->_accountFactory    = $accountFactory;
        $this->_currentCustomer   = $currentCustomer;
    }

    /**
     * @param $point
     * @throws \Exception
     */
    public function addPoint($point)
    {
        if ($this->_helper->getEnableModule() && !$this->_helper->getRewardTiersEnable()) {
            $this->addPointIntoQuote($point);
            $this->cart->getQuote()->setTotalsCollectedFlag(false);
            $this->cart->getQuote()->collectTotals()->save();
        }
    }

    /**
     * @param $points
     *
     * @throws \Exception
     */
    protected function addPointIntoQuote($points)
    {
        $quote = $this->checkoutSession->getQuote();

        if ($this->isValidToAddToCart($quote, $points)) {
            $quote->setData('reward_point', $points)->save();
            $rewardDiscountAmt = min($this->getRewardDiscountAmount($points), $quote->getBaseGrandTotal() + $quote->getRewardAmount());
            $quote->setData('reward_amount', $rewardDiscountAmt)->save();
        }
    }

    /**
     * @param $customerId
     *
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

    /**
     * @param $point
     *
     * @return mixed
     */
    public function getRewardDiscountAmount($point)
    {
        return $this->_helper->getRewardBaseAmount($point);
    }

    /**
     * @param $quote
     * @param $point
     *
     * @return bool
     */
    protected function isValidToAddToCart($quote, $point)
    {
        $customerId   = $this->_currentCustomer->getCustomerId();
        $currentPoint = $this->getAccount($customerId)->getPointsCurrent();
        if ($currentPoint < $point || $point < 0 || $this->getRewardDiscountAmount($point) > ceil($quote->getBaseGrandTotal() + $quote->getRewardAmount())) {
            return false;
        }
        return true;
    }

    /**
     * @param $quote
     * @param $usePointMax
     */
    public function updatePoint($quote, $usePointMax) {
        $quote->setData('reward_point', $usePointMax)->save();
        $quote->setData('reward_amount', $usePointMax)->save();
        $quote->setData('base_reward_amount', $usePointMax)->save();
    }

    /**
     * @param $quote
     */
    public function loadPoint($quote) {
        $spendingPoint = $this->_helper->getSpendingPoint();

        $customerId = $quote->getData('customer_id');
        $loadPoint = $this->getAccount($customerId);
        $couponCode = $quote->getData('coupon_code');
        $currentPoint = $loadPoint->getData('points_current');
        $rewardPoint = $quote->getData('reward_point');
        if ($rewardPoint != null && $rewardPoint != 0 && !$couponCode && $couponCode != null) {
            //Check admin case review hard point
            if ($spendingPoint == 1) {
                $usePointMax = $this->_helper->getMaximumPoint();
                //If the hard review point is larger than the existing point -> assign pointupdate = current point
                if ($usePointMax != 0) {
                    if ($usePointMax > $currentPoint) {
                        $point = $currentPoint;
                    } elseif ($rewardPoint > $usePointMax) {
                        $point = $usePointMax;
                    }
                    if (isset($point)) {
                        $this->updatePoint($quote, $point);
                    }
                }
            } elseif ($spendingPoint == 2) {
                //Config in percent
                $subtotal = $quote->getData('subtotal');
                $percentMaxOrder = $this->_helper->getPercentMaxOrder();
                if ($percentMaxOrder != 0) {
                    if ($this->_helper->getUpOrDown() == 'up') {
                        $pointMax = ceil(($percentMaxOrder / 100) * $subtotal);
                    } else {
                        $pointMax = floor(($percentMaxOrder / 100) * $subtotal);
                    }
                    //If % point max is greater than the number of existing points of the customer -> assign point = the number of points of the customer
                    if ($pointMax > $currentPoint) {
                        $point = $currentPoint;
                    } elseif ($rewardPoint > $pointMax) {
                        $point = $pointMax;
                    }
                    if (isset($point)) {
                        $this->updatePoint($quote, $point);
                    }
                }
            }
        }
    }
}
