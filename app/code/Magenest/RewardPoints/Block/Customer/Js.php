<?php

namespace Magenest\RewardPoints\Block\Customer;

/**
 * Class Js
 * @package Magenest\RewardPoints\Block\Customer
 */
class Js extends \Magento\Framework\View\Element\Template
{
    protected $_template = "Magenest_RewardPoints::customer/points/js.phtml";

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Js constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    public function getCustomer()
    {
        return $this->_customerSession;
    }

    /**
     * @return string
     */
    public function getApplyReferralCodeUrl()
    {
        return $this->getUrl('rewardpoints/customer/applyreferralcode');
    }
}