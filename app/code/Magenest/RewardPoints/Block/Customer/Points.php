<?php

namespace Magenest\RewardPoints\Block\Customer;

use Magenest\RewardPoints\Api\Data\MembershipInterface;
use Magenest\RewardPoints\Model\Membership;
use Magenest\RewardPoints\Model\RuleFactory;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magenest\RewardPoints\Model\AccountFactory;
use Magento\Backend\App\ConfigInterface;

/**
 * Class Points
 * @package Magenest\RewardPoints\Block\Customer
 */
class Points extends Template
{
    /**
     * @var RuleFactory
     */
    protected $_ruleFactory;

    /**
     * @var AccountFactory
     */
    protected $_accountFactory;

    /**
     * @var CurrentCustomer
     */
    protected $_currentCustomer;

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     *
     */
    const XML_PATH_DESCRIPTION = 'rewardpoints/point_config/reward_points_description';

    /**
     * @var \Magenest\RewardPoints\Model\ResourceModel\Transaction\Collection
     */
    protected $transactions;

    /**
     * @var \Magenest\RewardPoints\Model\ResourceModel\Transaction\CollectionFactory
     */
    protected $_transactionCollectionFactory;

    /**
     * @var \Magenest\RewardPoints\Model\ExpiredFactory
     */
    protected $expiredFactory;

    /**
     * @var ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magenest\RewardPoints\Cookie\ReferralCode
     */
    protected $referralCodeCookie;

    /**
     * @var \Magenest\RewardPoints\Model\ReferralFactory
     */
    protected $referralFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magenest\RewardPoints\Model\ReferralCouponFactory
     */
    protected $referralCouponFactory;

    /**
     * @var \Magenest\RewardPoints\Model\MyReferralFactory
     */
    protected $myReferralFactory;

    protected $_rewardProgramBlock;

    /**
     * Points constructor.
     * @param RewardProgramFactory $rewardProgram
     * @param \Magento\Customer\Model\Session $customerSession
     * @param Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magenest\RewardPoints\Helper\Data $helper
     * @param CurrentCustomer $currentCustomer
     * @param ConfigInterface $config
     * @param \Magenest\RewardPoints\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
     * @param \Magenest\RewardPoints\Model\ExpiredFactory $expiredFactory
     * @param AccountFactory $accountFactory
     * @param RuleFactory $ruleFactory
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magenest\RewardPoints\Cookie\ReferralCode $referralCodeCookie
     * @param \Magenest\RewardPoints\Model\ReferralFactory $referralFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magenest\RewardPoints\Model\ReferralCouponFactory $referralCouponFactory
     * @param \Magenest\RewardPoints\Model\MyReferralFactory $myReferralFactory
     * @param array $data
     */
    public function __construct(
        RewardProgramFactory $rewardProgram,
        \Magento\Customer\Model\Session $customerSession,
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenest\RewardPoints\Helper\Data $helper,
        CurrentCustomer $currentCustomer,
        ConfigInterface $config,
        \Magenest\RewardPoints\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory,
        \Magenest\RewardPoints\Model\ExpiredFactory $expiredFactory,
        AccountFactory $accountFactory,
        RuleFactory $ruleFactory,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magenest\RewardPoints\Cookie\ReferralCode $referralCodeCookie,
        \Magenest\RewardPoints\Model\ReferralFactory $referralFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magenest\RewardPoints\Model\ReferralCouponFactory $referralCouponFactory,
        \Magenest\RewardPoints\Model\MyReferralFactory $myReferralFactory,
        array $data = []
    ) {
        $this->_customerSession              = $customerSession;
        $this->_coreRegistry                 = $registry;
        $this->expiredFactory                = $expiredFactory;
        $this->_config                       = $config;
        $this->helper                        = $helper;
        $this->_currentCustomer              = $currentCustomer;
        $this->_transactionCollectionFactory = $transactionCollectionFactory;
        $this->_ruleFactory                  = $ruleFactory;
        $this->_accountFactory               = $accountFactory;
        $this->_filterProvider = $filterProvider;
        $this->referralCodeCookie            = $referralCodeCookie;
        $this->referralFactory = $referralFactory;
        $this->customerFactory = $customerFactory;
        $this->referralCouponFactory = $referralCouponFactory;
        $this->myReferralFactory = $myReferralFactory;
        $this->_rewardProgramBlock = $rewardProgram;
        parent::__construct($context, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Reward Points'));
    }

    /**
     * @return $this|\Magenest\RewardPoints\Model\ResourceModel\Transaction\Collection
     */
    public function getTransactions()
    {
        $customerId = $this->_currentCustomer->getCustomerId();
        if (!$this->transactions) {
            $this->transactions = $this->_transactionCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter('customer_id', $customerId)
                ->setOrder('id', 'desc');
        }

        return $this->transactions;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        $description = $this->_config->getValue(self::XML_PATH_DESCRIPTION);
        return $this->_filterProvider->getPageFilter()->filter($description);
    }

    /**
     * @return $this|Template
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'rewardPoints.transaction.history.pager'
        )->setCollection(
            $this->getTransactions()
        );
        $this->setChild('pager', $pager);
        $this->getTransactions()->load();

        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return \Magenest\RewardPoints\Model\Rule
     */
    public function getRule()
    {
        $ruleModel = $this->_ruleFactory->create();

        return $ruleModel;
    }

    /**
     * @return DataObject
     */
    public function getAccount()
    {
        $customerId = $this->_currentCustomer->getCustomerId();
        $account    = $this->_accountFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->getFirstItem();

        return $account;
    }

    /**
     * @param $transactionId
     *
     * @return mixed
     */
    public function getExpiryDate($transactionId)
    {
        $transactionCollection = $this->expiredFactory->create()->getCollection()->addFieldToFilter('transaction_id', $transactionId);
        $transactionModel      = $transactionCollection->getFirstItem();

        return $transactionModel->getData('expired_date');
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    public function getCustomer()
    {
        return $this->_customerSession;
    }

    /**
     * Get landing page value from config
     *
     * @return mixed
     */
    public function getLandingPage() {
        return $this->_scopeConfig->getValue('rewardpoints/point_config/landing_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get landing page link
     *
     * @return string
     */
    public function getLandingPageLink() {
        return '<a href="' . $this->getUrl($this->getLandingPage()) . '">' . __('View more') . '</a>';
    }

    /**
     * Get expiry type of transaction
     *
     * @param $id
     * @return bool|int
     */
    public function getExpiryType($id) {
        return $this->helper->getExpiryType($id);
    }

    /**
     * Get referral code currently set in cookie
     *
     * @return string
     */
    public function getReferralCodeCookie() {
        $referalCode = $this->referralCodeCookie->get();
        if ($referalCode === null) return '';
        return $referalCode;
    }

    /**
     * @return string
     */
    public function getApplyReferralCodeUrl($code, $customerId)
    {
        $referrerId     = $this->referralFactory->create()->load($code, 'referral_code')->getData('customer_id');
        $referralCode = $this->referralFactory->create()->load($customerId, 'customer_id')->getData('referral_code');
        $dataArr = [
            'customer_id' => $customerId,
            'apply_customer_id' => $referrerId,
            'referral_earning_type' => $this->helper->getReferralEarningType()
        ];
        $applyObj = new \Magento\Framework\DataObject($dataArr);
        // apply referral code
        $this->_eventManager->dispatch('apply_referral_code', ['applyObj' => $applyObj]);

        $this->sendCoupon($customerId,$referrerId,$code,$referralCode);

        //Load customer data after registration
        $customer = $this->customerFactory->create()->load($customerId);
        $customerEmail = $customer->getEmail();

        //Load data customer introduced
        $referrer = $this->customerFactory->create()->load($referrerId);
        $myReferral = $this->myReferralFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id',$referrerId)
            ->addFieldToFilter('email_referred',$customerEmail)->getFirstItem();
        if($myReferral->getId()){
            $myReferral->setStatus('1');
        }else{
            $myReferralData = [
                'email_referred' => $customerEmail,
                'customer_id' => $referrer->getData('entity_id'),
                'customer_referred_id' => $customerId,
                'status' => '1',
            ];
            $myReferral->addData($myReferralData);
        }
        return $myReferral->save();
    }


    /**
     * @param $customerId
     * @param $referrerId
     * @param $referralCode
     * @param $code
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendCoupon($customerId,$referrerId,$referralCode,$code)
    {
        $result = [];
        //Referral Coupon Are Awarded To (Option 2)
        $awardTo = $this->helper->couponAreAwardedTo();
        //When the coupon sent to the referrer (Option 3)
        $refered = $this->helper->whenReceivedCoupon(\Magenest\RewardPoints\Helper\Data::XML_PATH_REFERRAL_WHEN_COUPON_SENT_REFERED);

        //Coupon is sent to the presentee when the presentee has registered an account and the coupon is given to both or the presentee
        if(($refered == 0) && ($awardTo == 1 || $awardTo == 0)){
            $customer = $this->customerFactory->create()->load($customerId);
            $recipients = [
                'name' => $customer->getName(),
                'email' => $customer->getEmail()
            ];
            $couponRefered = $this->helper->sendCoupon($type = 'refered',$recipients);
            $result['refered'] = $couponRefered;
            $referralCouponModel = $this->referralCouponFactory->create();
            $referralCouponModel->addData([
                'customer_id' => $customerId,
                'coupon' => $couponRefered,
                'type' => '1',
                'referral_code' => $referralCode,
                'comment' => 'Refered Register'
            ]);
            $referralCouponModel->save();
        }

        $referrer = $this->helper->whenReceivedCoupon(\Magenest\RewardPoints\Helper\Data::XML_PATH_REFERRAL_WHEN_COUPON_SENT_REFERRER);
        //Coupon is sent to the referrer when the presentee has registered an account and the coupon is given to both or the referrer
        if(($referrer == 0) && ($awardTo == 2 || $awardTo == 0)){
            $customer = $this->customerFactory->create()->load($referrerId);
            $recipients = [
                'name' => $customer->getName(),
                'email' => $customer->getEmail()
            ];
            $couponReferrer = $this->helper->sendCoupon($type = 'referrer',$recipients);
            $result['referrer'] = $couponReferrer;
            $referralCouponModel = $this->referralCouponFactory->create();
            $referralCouponModel->addData([
                'customer_id' => $referrerId,
                'coupon' => $couponReferrer,
                'type' => '0',
                'referral_code' => $code,
                'comment' => 'Refered Register'
            ]);
            $referralCouponModel->save();
        }
        return $result;
    }

    /**
     * @param Membership $tier
     * @return array|\Magento\Framework\Phrase|mixed|null
     */
    public function getShortCondition(Membership $tier)
    {
        if ($tier->getData(MembershipInterface::GROUP_CONDITION_TYPE) == MembershipInterface::GROUP_CONDITION_TYPE_BY_SPEND_POINT) {
            return __('Spent %1 point(s)', $tier->getData(MembershipInterface::GROUP_CONDITION_VALUE));
        }

        return $tier->getData(MembershipInterface::GROUP_CONDITION_VALUE);
    }

    /**
     * @param DataObject $tierCheck
     * @param DataObject $customerTier
     * @return bool
     */
    public function isReachTier(DataObject $tierCheck, DataObject $customerTier)
    {
        $customerTierSortOrder = $customerTier->getData(MembershipInterface::SORT_ORDER) ?? 0;
        if ($customerTierSortOrder != 0 && $tierCheck->getData(MembershipInterface::SORT_ORDER) >= $customerTierSortOrder) {
            return true;
        }

        return false;
    }
}
