<?php

namespace Magenest\RewardPoints\Observer;

use Magenest\RewardPoints\Helper\Data;
use Magenest\RewardPoints\Model\AccountFactory;
use Magenest\RewardPoints\Model\ExpiredFactory;
use Magenest\RewardPoints\Model\ReferralPointsFactory;
use Magenest\RewardPoints\Model\RuleFactory;
use Magenest\RewardPoints\Model\TransactionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ApplyReferralCode implements ObserverInterface
{
    const CUSTOMER_TYPE_REFERRER = 1;
    const CUSTOMER_TYPE_REFERRED = 2;
    const POINTS_REFERRING = "points_referring";
    const POINTS_REFERRED = "points_referred";
    const EARNING_TYPE_BOTH = 0;
    const EARNING_TYPE_REFERRED = 1;
    const EARNING_TYPE_REFERRER = 2;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var AccountFactory
     */
    protected $accountFactory;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var ExpiredFactory
     */
    protected $expiredFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var ReferralPointsFactory
     */
    protected $referralPointsFactory;

    /**
     * ApplyReferralCode constructor.
     * @param RuleFactory $ruleFactory
     * @param Data $helper
     * @param AccountFactory $accountFactory
     * @param TransactionFactory $transactionFactory
     * @param ResourceConnection $resource
     * @param ExpiredFactory $expiredFactory
     * @param Session $customerSession
     */
    public function __construct(
        RuleFactory           $ruleFactory,
        Data                  $helper,
        AccountFactory        $accountFactory,
        TransactionFactory    $transactionFactory,
        ResourceConnection    $resource,
        ExpiredFactory        $expiredFactory,
        Session               $customerSession,
        ReferralPointsFactory $referralPointsFactory
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->helper = $helper;
        $this->accountFactory = $accountFactory;
        $this->transactionFactory = $transactionFactory;
        $this->resource = $resource;
        $this->expiredFactory = $expiredFactory;
        $this->customerSession = $customerSession;
        $this->referralPointsFactory = $referralPointsFactory;
    }

    public function execute(Observer $observer)
    {
        $applyObj = $observer->getData('applyObj');

        $ruleModel = $this->ruleFactory->create();

        $conditionType = $applyObj->getConditionType() ?? 'referafriend';

        $rules = $ruleModel->getCollection()->addFieldToFilter('condition', $conditionType);
        if (!empty($rules)) {
            foreach ($rules as $rule) {
                if ($this->helper->validateRule($rule)) {
                    $ruleId = $rule->getId();

                    $referEarningType = $applyObj->getReferralEarningType();

                    $pointReferrer = $this->getReferralPoint($ruleId, self::CUSTOMER_TYPE_REFERRER);
                    $pointReferred = $this->getReferralPoint($ruleId, self::CUSTOMER_TYPE_REFERRED);

                    switch ($referEarningType) {
                        case self::EARNING_TYPE_REFERRER:
                            $this->addPoints($ruleId, $applyObj->getApplyCustomerId(), $pointReferrer);
                            break;
                        case self::EARNING_TYPE_REFERRED:
                            $this->addPoints($ruleId, $applyObj->getCustomerId(), $pointReferred);
                            $this->customerSession->setData('rfa_customer_earned_points', $this->customerSession->getData('rfa_customer_earned_points') + $pointReferred);
                            break;
                        case self::EARNING_TYPE_BOTH:
                            $this->addPoints($ruleId, $applyObj->getApplyCustomerId(), $pointReferrer);
                            $this->addPoints($ruleId, $applyObj->getCustomerId(), $pointReferred);
                            $this->customerSession->setData('rfa_customer_earned_points', $this->customerSession->getData('rfa_customer_earned_points') + $pointReferred);
                            break;
                    }
                }
            }
        }
    }

    /**
     * Get referral points for coresponding customer
     *
     * @param $ruleId
     * @param $customerType
     * @return null
     */
    public function getReferralPoint($ruleId, $customerType)
    {
        if ($customerType == self::CUSTOMER_TYPE_REFERRER) {
            $key = self::POINTS_REFERRING;
        } elseif ($customerType == self::CUSTOMER_TYPE_REFERRED) {
            $key = self::POINTS_REFERRED;
        }
        $referralPointsTable = $this->referralPointsFactory->create()
            ->getCollection()
            ->addFieldToFilter('rule_id', $ruleId)
            ->getFirstItem();
        return $result = $referralPointsTable->getData($key);
    }

    /**
     * @param $customerId
     */
    public function addPoints($ruleId, $customerId, $referralPoints)
    {
        $helper = $this->helper;
        if ($referralPoints === null) $referralPoints = 0;
        $account = $this->accountFactory->create()->load($customerId, 'customer_id');
        if (!empty($account->getData())) {
            $pointsTotal = $account->getData('points_total') + $referralPoints;
            $pointsCurrent = $account->getData('points_current') + $referralPoints;
            $account->setData('points_total', $pointsTotal);
            $account->setData('points_current', $pointsCurrent);
            $account->save();
        } else {
            $pointsCurrent = $referralPoints;
            $data = [
                'customer_id' => $customerId,
                'points_total' => $referralPoints,
                'points_current' => $referralPoints,
                'points_spent' => 0,
                'loyalty_id' => '',
                'store_id' => ''
            ];
            $account->addData($data);
            $account->save();
        }

        $transactionModel = $this->transactionFactory->create();
        $data = [
            'rule_id' => -2,
            'customer_id' => $customerId,
            'points_change' => $referralPoints,
            'points_after' => isset($pointsCurrent) ? $pointsCurrent : 0,
            'comment' => 'Referer code'
        ];
        $transactionModel->addData($data)->save();

        $expiredModel = $this->expiredFactory->create();
        $timeToExpired = (int)$helper->getTimeExpired();
        $timeExpired = strtotime("+" . $timeToExpired . " days");
        $dateExpired = date("Y-m-d H:i:s", $timeExpired);
        $data = [
            'transaction_id' => $transactionModel->getId(),
            'rule_id' => -2,
            'customer_id' => $customerId,
            'points_change' => $referralPoints,
            'expired_date' => $dateExpired,
            'status' => 'available',
            'expiry_type' => (bool)$timeToExpired
        ];
        $expiredModel->addData($data)->save();
    }
}
