<?php

namespace Magenest\RewardPoints\Block\Cart;

use Magenest\RewardPoints\Model\Api\ProcessPoint;
use Magento\Checkout\Model\Session as CheckoutSession;

/**
 * Class Js
 * @package Magenest\RewardPoints\Block\Customer
 */
class MultiShipping extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magenest\RewardPoints\Model\AccountFactory
     */
    protected $_accountFactory;

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $helper;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magenest\RewardPoints\Helper\AddPoint
     */
    protected $addPoint;

    /**
     * @var ProcessPoint
     */
    protected $_processPoint;

    /**
     * MultiShipping constructor.
     * @param ProcessPoint $processPoint
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magenest\RewardPoints\Model\AccountFactory $accountFactory
     * @param \Magenest\RewardPoints\Helper\Data $helper
     * @param CheckoutSession $checkoutSession
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magenest\RewardPoints\Helper\AddPoint $addPoint
     * @param array $data
     */
    public function __construct(
        ProcessPoint $processPoint,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magenest\RewardPoints\Model\AccountFactory $accountFactory,
        \Magenest\RewardPoints\Helper\Data $helper,
        CheckoutSession $checkoutSession,
        \Magento\Checkout\Model\Cart $cart,
        \Magenest\RewardPoints\Helper\AddPoint $addPoint,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->_accountFactory = $accountFactory;
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->cart = $cart;
        $this->addPoint = $addPoint;
        $this->_processPoint = $processPoint;
        parent::__construct($context, $data);
    }

    /**
     * @return int|string
     */
    public function currentPoint()
    {
        return $this->_processPoint->getCurrentPoint();
    }

    public function getConfigPoint()
    {
        return $this->helper->getRewardBaseAmount(null);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrency()
    {
        return $this->checkoutSession->getQuote()->getBaseCurrencyCode();
    }

    /**
     * @return float|int|\Magento\Customer\Model\Session|mixed
     */
    public function getMaxAppliedPoint()
    {
        return $this->_processPoint->calculateMaxAppliedPoint();
    }

    /**
     * @return mixed|string
     */
    public function getRewardPoint()
    {
        $quote = $this->cart->getQuote();
        $spendingConfig = $this->helper->getSpendingConfigurationEnable();
        //Check Spending Configuration In Backend
        if ($spendingConfig) {
            $this->addPoint->loadPoint($quote);
        }
        //Get point in table quote
        $rewardPoint = $quote->getData('reward_point');
        if ($rewardPoint == 0 || $rewardPoint == null) {
            $rewardPoint = '';
        }
        $arr = explode('.', $rewardPoint);
        $rewardPoint = reset($arr);
        return $rewardPoint;
    }
}
