<?php

namespace Magenest\RewardPoints\Block\Adminhtml\Order;

use Magento\Framework\View\Element\Template;

/**
 * Class RedeemPoints
 * @package Magenest\RewardPoints\Block\Adminhtml\Order
 */
class RedeemPoints extends Template
{
    public static $md5checksum = 'checksum';
    const XML_PATH_CONFIG_POINT = 'rewardpoints/point_config/points_money';
    const XML_PATH_CONFIG_TIER_ENABLE = 'rewardpoints/reward_tiers/reward_tiers_enable';
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;
    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $helperData;
    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $sessionQuote;

    /**
     * @var \Magenest\RewardPoints\Model\AccountFactory
     */
    protected $accountFactory;

    /**
     * RedeemPoints constructor.
     * @param Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magenest\RewardPoints\Helper\Data $helperData
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magenest\RewardPoints\Model\AccountFactory $accountFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magenest\RewardPoints\Helper\Data $helperData,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magenest\RewardPoints\Model\AccountFactory $accountFactory,
        array $data = []
    ) {
        $this->sessionQuote  = $sessionQuote;
        $this->_coreRegistry = $registry;
        $this->quoteFactory  = $quoteFactory;
        $this->helperData    = $helperData;
        $this->accountFactory = $accountFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getApplyRedeemPointsUrl()
    {
        return $this->getUrl('rewardpoints/quote/add');
    }

    /**
     * @return string
     */
    public function getCancelRedeemPointsUrl()
    {
        return $this->getUrl('rewardpoints/quote/remove');
    }
    /**
     * @return string
     */
    public function getProcessTierUrl()
    {
        return $this->getUrl('rewardpoints/quote/process');
    }

    /**
     * @return mixed
     */
    public function getCustomerRewardPoint()
    {
        $customerId = $this->sessionQuote->getCustomerId();
        $accountModel = $this->accountFactory->create()->load($customerId, 'customer_id');
        return $accountModel->getData('points_current');
    }

    /**
     * @param $point
     *
     * @return float|int
     */
    public function getRewardBaseAmount($point)
    {
        return $this->helperData->getRewardBaseAmount($point);
    }

    /**
     * @return mixed
     */
    public function getConfigPoint()
    {
        return $this->helperData->getConfigData(self::XML_PATH_CONFIG_POINT);
    }

    /**
     * @return mixed
     */
    public function getTierEnable(){
        return $this->helperData->getConfigData(self::XML_PATH_CONFIG_TIER_ENABLE);
    }
    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getSessionQuote()
    {
        return $this->sessionQuote->getQuote();
    }

    /**
     * @return bool
     */
    public function enableDisplayInputRedeem()
    {
        $quoteSession = $this->getSessionQuote();
        $rewardPoint  = $quoteSession->getData('reward_point');
        $rewardAmount = $quoteSession->getData('reward_amount');
        if (isset($rewardAmount) AND isset($rewardPoint) AND $rewardAmount > 0  AND $rewardPoint > 0) {
            return false;
        }
        return true;
    }

    /**
     * @return mixed
     */
    public function rewardPointHaveRedeemed()
    {
        $quoteSession = $this->getSessionQuote();
        if ($this->enableDisplayInputRedeem() == false) {
            return $rewardPoint = $quoteSession->getData('reward_point') * 1;
        } else return 0;
    }

    /**
     * @return mixed
     */
    public function spendingConfigurationEnable() {
        return $this->helperData->getSpendingConfigurationEnable();
    }

    /**
     * @return mixed
     */
    public function percentMaxOrder() {
        return $this->helperData->getPercentMaxOrder();
    }

    /**
     * @return mixed
     */
    public function spendingPoint() {
        return $this->helperData->getSpendingPoint();
    }

    /**
     * @return mixed
     */
    public function maximumPoint() {
        return $this->helperData->getMaximumPoint();
    }
}
