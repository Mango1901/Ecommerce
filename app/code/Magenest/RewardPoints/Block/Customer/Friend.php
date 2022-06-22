<?php

namespace Magenest\RewardPoints\Block\Customer;

use Magenest\RewardPoints\Model\RuleFactory;
use Magento\Framework\View\Element\Template;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magenest\RewardPoints\Model\AccountFactory;
use Magento\Backend\App\ConfigInterface;

/**
 * Class Points
 * @package Magenest\RewardPoints\Block\Customer
 */
class Friend extends Template
{
    /**
     *
     */
    const XML_PATH_DESCRIPTION = 'rewardpoints/point_config/reward_points_description';

    /**
     * @var CurrentCustomer
     */
    protected $_currentCustomer;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magenest\RewardPoints\Model\ResourceModel\Transaction\Collection
     */
    protected $transactions;

    /**
     * @var ConfigInterface
     */
    protected $_config;

    /**
     * @var null|\Magento\Framework\App\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magenest\RewardPoints\Model\ReferralFactory
     */
    protected $referralFactory;

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $referHelper;

    /**
     * @var \Magenest\RewardPoints\Cookie\ReferralCode
     */
    protected $referralCodeCookie;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var AccountFactory
     */
    protected $accountFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magenest\RewardPoints\Observer\CustomerRegistration
     */
    protected $customerRegistration;

    /**
     * Friend constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CurrentCustomer $currentCustomer
     * @param ConfigInterface $config
     * @param \Magenest\RewardPoints\Model\ReferralFactory $referralFactory
     * @param \Magenest\RewardPoints\Helper\Data $referHelper
     * @param \Magenest\RewardPoints\Cookie\ReferralCode $referralCodeCookie
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param AccountFactory $accountFactory
     * @param \Magenest\RewardPoints\Observer\CustomerRegistration $customerRegistration
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        CurrentCustomer $currentCustomer,
        ConfigInterface $config,
        \Magenest\RewardPoints\Model\ReferralFactory $referralFactory,
        \Magenest\RewardPoints\Helper\Data $referHelper,
        \Magenest\RewardPoints\Cookie\ReferralCode $referralCodeCookie,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magenest\RewardPoints\Model\AccountFactory $accountFactory,
        \Magenest\RewardPoints\Observer\CustomerRegistration $customerRegistration,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_coreRegistry = $registry;
        $this->_config = $config;
        $this->_currentCustomer = $currentCustomer;
        $this->referralFactory = $referralFactory;
        $this->referHelper = $referHelper;
        $this->referralCodeCookie = $referralCodeCookie;
        $this->formKey = $formKey;
        $this->accountFactory = $accountFactory;
        $this->customerRegistration = $customerRegistration;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function enableInputReferralCode()
    {
        $customerId = $this->_customerSession->getId();
        $code = $this->referralFactory->create()->load($customerId, 'customer_id')->getData('code');
        if ($code == null) {
            return true;
        }
        return false;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getAccount()
    {
        $customerId = $this->_currentCustomer->getCustomerId();
        $account = $this->accountFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->getFirstItem();

        return $account;
    }

    /**
     * @return bool|mixed
     */
    public function isEnableReferralCode()
    {
        return $this->referHelper->getEnableModule();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getReferralCode()
    {
        $customerId = $this->_customerSession->getId();
        $referralCode = $this->referralFactory->create()->load($customerId, 'customer_id');
        $code = $referralCode->getData('referral_code');
        if (empty($code)) {
            $code = $this->customerRegistration->createCode();
            $referral = $this->referralFactory->create();
            $referral->setData([
                'customer_id' => $customerId,
                'referral_code' => $code
            ]);
            $referral->save();
        }
        return $code;
    }

    public function pagingRecords($tabs)
    {
        $limit = $this->getRequest()->getParam('limit');
        if ($limit != null && $tabs == 'referral') {
            return 'display: block';
        } elseif ($limit == null && $tabs == 'general') {
            return 'display: block';
        }
        return 'display: none';
    }

    public function actvieClass($tabs)
    {
        $limit = $this->getRequest()->getParam('limit');
        if ($limit != null && $tabs == 'referral') {
            return 'active';
        } elseif ($limit == null && $tabs == 'general') {
            return 'active';
        }
        return '';
    }


    /**
     * @return string
     */
    public function getApplyReferralCodeUrl()
    {
        return $this->getUrl('rewardpoints/customer/applyreferralcode');
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    public function getCustomer()
    {
        return $this->_customerSession;
    }

    /**
     * Get referral link
     *
     * @return string
     */
    public function getReferralLink()
    {
        try {
            return $this->getBaseUrl() . $this->referHelper->getReferPath() . '?referralcode=' . $this->getReferralCode();
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Get referral code currently set in cookie
     *
     * @return string
     */
    public function getReferralCodeCookie()
    {
        $referalCode = $this->referralCodeCookie->getCookieName();
        if ($referalCode === null) {
            return '';
        }
        return $referalCode;
    }

    /**
     * Get action for send referral link to friend via email form action
     *
     * @return string
     */
    public function getSendToFriendFormAction()
    {
        return $this->getBaseUrl() . 'rewardpoints/referral/sendemail';
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

}
