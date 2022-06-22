<?php

namespace Magenest\RewardPoints\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magenest\RewardPoints\Helper\Data;


class SendCoupon implements ObserverInterface
{
    /**
     * Order Model
     *
     * @var \Magento\Sales\Model\Order $order
     */
    protected $order;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var \Magenest\RewardPoints\Model\MyReferralFactory
     */
    protected $myReferralFactory;

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
     * SendCoupon constructor.
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Data $helper
     * @param \Magenest\RewardPoints\Model\MyReferralFactory $myReferralFactory
     * @param \Magenest\RewardPoints\Model\ReferralFactory $referralFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magenest\RewardPoints\Model\ReferralCouponFactory $referralCouponFactory
     */
    public function __construct(
        \Magento\Sales\Model\Order $order,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Data $helper,
        \Magenest\RewardPoints\Model\MyReferralFactory $myReferralFactory,
        \Magenest\RewardPoints\Model\ReferralFactory $referralFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magenest\RewardPoints\Model\ReferralCouponFactory $referralCouponFactory
    )
    {
        $this->order = $order;
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        $this->myReferralFactory = $myReferralFactory;
        $this->referralFactory = $referralFactory;
        $this->customerFactory = $customerFactory;
        $this->referralCouponFactory = $referralCouponFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getData('order');
        $orders = $observer->getData('orders');
        if (!empty($order) && empty($orders)) {
            $orders = [$order];
        }
        foreach ($orders as $order) {
            if ($order->getState() === \Magento\Sales\Model\Order::STATE_NEW) {
                $customerId = $order->getCustomerId();
                $result = [];
                //Referral Coupon Are Awarded To (Option 2)
                $awardTo = $this->_helper->couponAreAwardedTo();
                //When the coupon sent to the referrer (Option 3)
                $refered = $this->_helper->whenReceivedCoupon(\Magenest\RewardPoints\Helper\Data::XML_PATH_REFERRAL_WHEN_COUPON_SENT_REFERED);
                $referral = $this->myReferralFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('customer_referred_id',$customerId)
                    ->getFirstItem();
                $referrerId = '';
                //Coupons are sent to the referred person when the referred person has signed up for an account + purchase and the coupon is given to both or the referrer
                if (($refered == 1) && ($awardTo == 1 || $awardTo == 0)) {
                    if ($referral->getId()) {
                        $referrerId = $referral->getData('customer_id');

                        $referralCode = $this->referralFactory->create()->load($referral->getData('customer_id'), 'customer_id');
                        $code = $referralCode->getData('referral_code');
                        /** @var \Magento\Customer\Model\Customer $customer */
                        $customer = $this->customerFactory->create()->load($customerId);
                        $recipients = [
                            'name' => $customer->getName(),
                            'email' => $customer->getEmail()
                        ];
                        $couponRefered = $this->_helper->sendCoupon($type = 'refered', $recipients);
                        $result['refered'] = $couponRefered;
                        $referralCouponModel = $this->referralCouponFactory->create();
                        $referralCouponModel->addData([
                            'customer_id' => $customerId,
                            'coupon' => $couponRefered,
                            'type' => '1',
                            'referral_code' => $code,
                            'comment' => 'Refered Purchase Order'
                        ]);
                        $referralCouponModel->save();
                    }
                }
                $referrer = $this->_helper->whenReceivedCoupon(\Magenest\RewardPoints\Helper\Data::XML_PATH_REFERRAL_WHEN_COUPON_SENT_REFERRER);
                //Coupons are sent to the referrer once the referrer has registered an account + purchase and the coupon is given to both or the referrer
                if (($referrer == 1) && ($awardTo == 2 || $awardTo == 0)) {
                    if ($referral->getId()) {
                        $referrerId = $referral->getData('customer_id');
                        /** @var \Magento\Customer\Model\Customer $customer */
                        $customer = $this->customerFactory->create()->load($referrerId);
                        $recipients = [
                            'name' => $customer->getName(),
                            'email' => $customer->getEmail()
                        ];
                        $couponReferrer = $this->_helper->sendCoupon($type = 'referrer', $recipients);
                        $result['referrer'] = $couponReferrer;
                        $referralCouponModel = $this->referralCouponFactory->create();
                        $referralCouponModel->addData([
                            'customer_id' => $referrerId,
                            'coupon' => $couponReferrer,
                            'type' => '0',
                            'referral_code' => $code,
                            'comment' => 'Refered Purchase Order'
                        ]);
                        $referralCouponModel->save();
                    }
                }

                if ($referrerId != '') {
                    $this->updateHistory($customerId, $referrerId);
                }
            }
        }
    }

    /**
     * @param $customerId
     * @param $referrerId
     */
    public function updateHistory($customerId, $referrerId) {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->customerFactory->create()->load($customerId);
        $customerEmail = $customer->getEmail();
        /** @var \Magento\Customer\Model\Customer $referrer */
        $referrer = $this->customerFactory->create()->load($referrerId);

        $myReferral = $this->myReferralFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $referrerId)
            ->addFieldToFilter('email_referred', $customerEmail)->getFirstItem();
        if ($myReferral->getId()) {
            $myReferral->setStatus('2');
        } else {
            $myReferralData = [
                'email_referred' => $customerEmail,
                'customer_id' => $referrer->getId(),
                'status' => '2',
            ];
            $myReferral->addData($myReferralData);
        }
        $myReferral->save();
    }
}
