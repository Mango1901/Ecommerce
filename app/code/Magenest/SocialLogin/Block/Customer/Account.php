<?php

namespace Magenest\SocialLogin\Block\Customer;


use Magento\Framework\View\Element\Template;

/**
 * Class Account
 * @package Magenest\SocialLogin\Block\Customer
 */
class Account extends Template
{
    /**
     * @var \Magenest\SocialLogin\Model\ResourceModel\SocialAccount\CollectionFactory
     */
    protected $socialAccountCollection;

    /**
     * @var \Magenest\SocialLogin\Helper\SocialLogin
     */
    protected $socialLoginHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Account constructor.
     * @param \Magenest\SocialLogin\Model\ResourceModel\SocialAccount\CollectionFactory $socialAccountCollection
     * @param \Magenest\SocialLogin\Helper\SocialLogin $socialLoginHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magenest\SocialLogin\Model\ResourceModel\SocialAccount\CollectionFactory $socialAccountCollection,
        \Magenest\SocialLogin\Helper\SocialLogin $socialLoginHelper,
        \Magento\Customer\Model\Session $customerSession,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->socialAccountCollection = $socialAccountCollection;
        $this->socialLoginHelper       = $socialLoginHelper;
        $this->customerSession         = $customerSession;
    }

    /**
     * @return mixed
     */
    public function getAllAccounts()
    {
        return $this->socialAccountCollection->create()
                                             ->addFieldToFilter('customer_id', $this->customerSession->getCustomerId());
    }

    /**
     * @return string[]
     */
    public function getAllSocialTypes(): array
    {
        return $this->socialLoginHelper->getAllSocialTypes();
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->customerSession->getCustomer();
    }
}
