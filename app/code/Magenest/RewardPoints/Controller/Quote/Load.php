<?php

namespace Magenest\RewardPoints\Controller\Quote;

use Magento\Framework\App\Action\Context;
use Magenest\RewardPoints\Helper\Data;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Load
 * @package Magenest\RewardPoints\Controller\Quote
 */
class Load extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Quote\Model\Quote
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
     * Load constructor.
     *
     * @param Context $context
     * @param CurrentCustomer $currentCustomer
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        CurrentCustomer $currentCustomer,
        \Magento\Checkout\Model\Session $checkoutSession,
        Data $helper
    ) {
        parent::__construct($context);
        $this->checkoutSession  = $checkoutSession;
        $this->helper           = $helper;
        $this->_currentCustomer = $currentCustomer;

    }

    /**
     * Get the json string that represent the gift card in quote
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        //reward point is applied for current quote
        $reward_point = $this->checkoutSession->getQuote()->getData('reward_point');

        //totalReward point from the account
        //Reward points amount that could apply for current cart

        $result['rewardPointAmount'] = floatval($reward_point);
        $pointEarned = $this->helper->getPointsEarnIncludeMembershipReward($this->checkoutSession->getQuote());
        $result['pointEarned'] = $pointEarned;
        $result['pointLabel'] = $this->helper->getPointUnit();

        /*
          * @var \Magento\Framework\Controller\Result\Json $resultJson
        */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);

        return $resultJson;
    }
}
