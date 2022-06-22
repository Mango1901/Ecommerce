<?php

namespace Magenest\RewardPoints\Controller\Customer;

/**
 * Class ApplyReferralCode
 * @package Magenest\RewardPoints\Controller\Customer
 */
class ApplyReferralCode extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Magenest\RewardPoints\Model\ReferralFactory
     */
    protected $referralFactory;

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $referHelper;

    /**
     * @var \Magenest\RewardPoints\Model\ReferralPointsFactory
     */
    protected $referralPointsFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /** @var \Magenest\RewardPoints\Model\ReferralCouponFactory $referralCouponFactory */
    protected $referralCouponFactory;

    /** @var \Magenest\RewardPoints\Model\MyReferralFactory $myReferralFactory */
    protected $myReferralFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magenest\RewardPoints\Block\Customer\Points
     */
    protected $pointsBlock;

    /**
     * ApplyReferralCode constructor.
     * @param \Magenest\RewardPoints\Block\Customer\Points $points
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magenest\RewardPoints\Model\ReferralFactory $referralFactory
     * @param \Magenest\RewardPoints\Helper\Data $referHelper
     * @param \Magenest\RewardPoints\Model\ReferralPointsFactory $referralPointsFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magenest\RewardPoints\Model\ReferralCouponFactory $referralCouponFactory
     * @param \Magenest\RewardPoints\Model\MyReferralFactory $myReferralFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        \Magenest\RewardPoints\Block\Customer\Points $points,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magenest\RewardPoints\Model\ReferralFactory $referralFactory,
        \Magenest\RewardPoints\Helper\Data $referHelper,
        \Magenest\RewardPoints\Model\ReferralPointsFactory $referralPointsFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magenest\RewardPoints\Model\ReferralCouponFactory $referralCouponFactory,
        \Magenest\RewardPoints\Model\MyReferralFactory $myReferralFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->referralFactory = $referralFactory;
        $this->referHelper = $referHelper;
        $this->referralPointsFactory = $referralPointsFactory;
        $this->customerSession = $customerSession;
        $this->referralCouponFactory = $referralCouponFactory;
        $this->myReferralFactory = $myReferralFactory;
        $this->customerFactory = $customerFactory;
        $this->pointsBlock = $points;
        parent::__construct($context);
    }

    /**
     * @param $customerId
     * @param $code
     *
     * @return bool
     */
    public function checkCode($customerId, $code)
    {
        $referral = $this->referralFactory->create()->load($customerId, 'customer_id');
        $referralCode = $referral->getData('referral_code');
        $myReferral = $this->referralFactory->create()
            ->getCollection()
            ->addFieldToFilter('referral_code',$code)->getFirstItem();
        $customerReferral = $myReferral->getData('customer_id');

        $myReferralData = $this->myReferralFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id',$customerId)
            ->addFieldToFilter('customer_referred_id',$customerReferral)
            ->getFirstItem();

        if ($referralCode == $code || $myReferralData->getData('id') || count($myReferral->getData()) <= 0) {
            return false;
        }
        return true;
    }

    /**
     * @param $customerId
     * @param $code
     * @throws \Exception
     */
    public function applyCode($customerId, $code)
    {
        $referral = $this->referralFactory->create()->load($customerId, 'customer_id');
        $referral->setData('code', $code);
        $referral->save();

        $referHelper = $this->referHelper;
        if (!$referHelper->getEnableModule() ||
            !$this->referHelper->isRewardPointsModuleEnabled() ||
            !$referHelper->getEnableModule()) {
            return;
        }

        $this->pointsBlock->getApplyReferralCodeUrl($code, $customerId);
    }

    /**
     * @param $code
     *
     * @return mixed
     */
    public function getCustomerIdByCode($code)
    {
        $customerId = $this->referralFactory->create()->load($code, 'referral_code')->getData('customer_id');

        return $customerId;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $code = $this->getRequest()->getParam('applyCode');
            $customerId = $this->getRequest()->getParam('customerId');
            if (isset($code) && !empty($code) && isset($customerId) && $this->checkCode($customerId, $code)) {
                $this->applyCode($customerId, $code);
                $response = [
                    'success' => true,
                    'points' => number_format($this->customerSession->getData('rfa_customer_earned_points')),
                ];
                if (!$this->customerSession->getData('rfa_customer_earned_points') &&
                    $this->getRequest()->getParam('apply_success_noti')) {
                    $this->customerSession->setData('apply_success_noti', 1);
                }
            } else {
                $response = [
                    'success' => false
                ];
            }
        } catch (\Exception $exception) {
            $response = [
                'success' => false,
                'message' => $exception->getMessage()
            ];
        }
        $resultJson = $this->_resultJsonFactory->create();
        return $resultJson->setData($response);
    }

    /**
     * @param $customerId
     * @param $referrerId
     * @param $referralCode
     * @param $code
     * @return array
     * @throws \Exception
     */
    public function sendCoupon($customerId, $referrerId, $referralCode, $code)
    {
        $result = [];
        $awardTo = $this->referHelper->couponAreAwardedTo();
        $refered = $this->referHelper->whenReceivedCoupon(\Magenest\RewardPoints\Helper\Data::XML_PATH_REFERRAL_WHEN_COUPON_SENT_REFERED);

        if (($refered == 0) && ($awardTo == 1 || $awardTo == 0)) {
            /** @var \Magento\Customer\Model\Customer $customer */
            $customer = $this->customerFactory->create()->load($customerId);
            $recipients = [
                'name' => $customer->getName(),
                'email' => $customer->getEmail()
            ];
            $couponRefered = $this->referHelper->sendCoupon($type = 'refered', $recipients);
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
        $referrer = $this->referHelper->whenReceivedCoupon(\Magenest\RewardPoints\Helper\Data::XML_PATH_REFERRAL_WHEN_COUPON_SENT_REFERRER);
        if (($referrer == 0) && ($awardTo == 2 || $awardTo == 0)) {
            /** @var \Magento\Customer\Model\Customer $customer */
            $customer = $this->customerFactory->create()->load($referrerId);
            $recipients = [
                'name' => $customer->getName(),
                'email' => $customer->getEmail()
            ];
            $couponReferrer = $this->referHelper->sendCoupon($type = 'referrer', $recipients);
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
}
