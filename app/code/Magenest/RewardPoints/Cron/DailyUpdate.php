<?php

namespace Magenest\RewardPoints\Cron;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magenest\RewardPoints\Model\RuleFactory;
use Magenest\RewardPoints\Helper\Data;
use Magenest\RewardPoints\Model\ExpiredFactory;
use Magenest\RewardPoints\Model\AccountFactory;
use Magenest\RewardPoints\Model\TransactionFactory;

/**
 * Class DailyUpdate
 * @package Magenest\RewardPoints\Cron
 */
class DailyUpdate
{
    private static $messagepointsexpired = "Expiry point";

    private static $referercode = ".";
    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * @var RuleFactory
     */
    protected $_ruleFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepositoryInterface;

    /**
     * @var ExpiredFactory
     */
    protected $_expiredFactory;

    /**
     * @var TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @var AccountFactory
     */
    protected $_accountFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * DailyUpdate constructor.
     * @param RuleFactory $ruleFactory
     * @param DateTime $dateTime
     * @param ExpiredFactory $expiredFactory
     * @param AccountFactory $accountFactory
     * @param TransactionFactory $transactionFactory
     * @param Data $helper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Customer\Model\ResourceModel\Customer $customerResource
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        RuleFactory $ruleFactory,
        DateTime $dateTime,
        ExpiredFactory $expiredFactory,
        AccountFactory $accountFactory,
        TransactionFactory $transactionFactory,
        Data $helper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\ResourceModel\Customer $customerResource,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->_date           = $dateTime;
        $this->_expiredFactory = $expiredFactory;
        $this->_accountFactory = $accountFactory;
        $this->_transactionFactory          = $transactionFactory;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_ruleFactory                 = $ruleFactory;
        $this->helper                       = $helper;
        $this->_customerResource            = $customerResource;
        $this->_customerFactory = $customerFactory;
    }

    /**
     * @throws \Exception
     */
    public function execute()
    {
        $ruleModel = $this->_ruleFactory->create();
        $rules     = $ruleModel->getCollection()->addFieldToFilter('condition', 'birthday');
        if ($rules->getSize()) {
            foreach ($rules as $rule) {
                if (!$this->helper->validateRule($rule)) continue;
                $ruleId    = $rule->getId();
                $readConnection = $this->_customerResource->getConnection('write');
                $today          = new \DateTime();
                $birthDay       = $today->format('m-d');
                $select         = $readConnection->select()->from(
                    $this->_customerResource->getEntityTable(),
                    [$this->_customerResource->getEntityIdField()]
                )->where('DATE_FORMAT(dob, "%m-%d")=?', $birthDay);
                $customers      = $readConnection->fetchAssoc($select);
                foreach ($customers as $customer) {
                    $customerId = $customer['entity_id'];
                    $customer = $this->_customerFactory->create()->load($customerId);
                    $this->helper->addPoints($customer, $ruleId, null, null);

                    $recipients = [
                        'name' => $customer->getLastname() . ' ' . $customer->getFirstname(),
                        'email' => $customer->getEmail()
                    ];
                    $this->helper->sendCouponForAffiliateAndBirthday(
                        $recipients,
                        Data::XML_PATH_BIRTHDAY_APPLY_COUPON_BIRTHDAY,
                        Data::XML_PATH_BIRTHDAY_SHOPPING_CART_RULE_BIRTHDAY,
                        Data::XML_PATH_BIRTHDAY_EMAIL_TEMPLATE_BIRTHDAY
                    );
                }
            }
        }
        //Check expiration point
        $this->getxEpirationpoint();

    }

    /**
     * @throws \Exception
     */
    public function getxEpirationpoint()
    {
        $now = strftime('%Y-%m-%d', time() );
        $expiryLimit = $this->helper->getTimeExpired()*1;
        $expiredModel    = $this->_expiredFactory->create();
        $listPointOfUser = $expiredModel->getCollection()
            ->addFieldToFilter('status', 'available')
            ->setOrder('expired_date', 'ASC');
        try {
            if ($listPointOfUser->getSize()) {
                foreach ($listPointOfUser as $userPoint) {
                    $timeExpired = date('Y-m-d', strtotime($userPoint->getExpiredDate()));
                    $expiryType = $userPoint->getExpiryType();
                    if ($expiryType === null) $expiryType = (bool)$expiryLimit;

                    //Send Email Expiry
                    $checkEmail = $userPoint->getCheckSendEmail();
                    if ($checkEmail == null) {
                        $this->getSendEmailExpired($userPoint);
                    }

                    //send email Referral
                    $referral = $userPoint->getRuleId();
                    $checkReferral = $userPoint->getCheckReferral();
                    if ($referral == -2 && $checkReferral == null) {
                        $this->getSendEmailReferral($userPoint);
                    }

                    if ($expiryType && $now > $timeExpired) {
                        $accountModel = $this->_accountFactory->create();
                        $accountModel      = $accountModel->getCollection()->addFieldToFilter('customer_id', $userPoint->getCustomerId())->getFirstItem();
                        $data                   = $accountModel->getData();
                        $data['customer_id']    = $userPoint->getCustomerId();
                        //Send Email Expired date
                        if ($this->helper->getBalanceEmailEnable()) {
                            $rule_id = -12;
                            $account = $accountModel;
                            $idCustomer = $userPoint->getCustomerId();
                            $customer = $this->_customerFactory->create()->load($idCustomer);
                            $point = -$userPoint->getPointsChange();
                            $ruleTitle = self::$messagepointsexpired;
                            $this->helper->getSendEmail(null, $account, $point, $rule_id, $ruleTitle, $customer);
                        }
                        $data['points_current'] = $accountModel->getData('points_current') - $userPoint->getPointsChange();
                        $accountModel->addData($data)->save();

                        $transactionModel = $this->_transactionFactory->create();
                        $point            = -$userPoint->getPointsChange();
                        $data             = [
                            'rule_id'       => '-3',
                            'order_id'      => $userPoint->getOrderId(),
                            'customer_id'   => $userPoint->getCustomerId(),
                            'points_change' => $point,
                            'points_after'  => $accountModel->getData('points_current'),
                            'product_id'    => $userPoint->getProductId(),
                            'comment'       => 'Expired. Transaction ID : ' . $userPoint->getTransactionId()
                        ];
                        $transactionModel->addData($data)->save();

                        $details          = $expiredModel->load($userPoint->getId());
                        $detail           = $details->getData();
                        $detail['status'] = 'expired';
                        $details->setData($detail)->save();

                    }
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $userPoint
     * @throws \Exception
     */
    public function getSendEmailExpired($userPoint) {
        $expiredModel    = $this->_expiredFactory->create();
        $now = date("Y-m-d");
        $sendBefore = (int)$this->helper->getSendBefore();
        $timeExpired = $userPoint->getExpiredDate();
        $dateSendEmail = date('Y-m-d',strtotime($timeExpired . "-" . $sendBefore . " days"));
        $expiryType = $userPoint->getExpiryType();
        if ($expiryType === null) $expiryType = (bool)$sendBefore;

        if ($this->helper->getExpirationEmailEnable()) {
            if ($sendBefore != 0) {
                if ($expiryType && $now == $dateSendEmail) {
                    $accountModel = $this->_accountFactory->create();
                    $accountModel = $accountModel->getCollection()->addFieldToFilter('customer_id',
                        $userPoint->getCustomerId())->getFirstItem();
                    $data = $accountModel->getData();
                    $data['customer_id'] = $userPoint->getCustomerId();
                    $customerId = $userPoint->getCustomerId();
                    $customer = $this->_customerFactory->create()->load($customerId);
                    $this->helper->getSendEmailExpired($userPoint, $customer, $sendBefore);
                    $this->getUpdateData($expiredModel, $userPoint, $sendBefore);
                }
            }
        }
    }

    /**
     * @param $userPoint
     */
    public function getSendEmailReferral($userPoint) {
        if ($this->helper->getBalanceEmailEnable()) {
            $expiredModel    = $this->_expiredFactory->create();
            $accountModel = $this->_accountFactory->create();
            $accountModel = $accountModel->getCollection()->addFieldToFilter('customer_id',
                $userPoint->getCustomerId())->getFirstItem();
            $data = $accountModel->getData();
            $data['customer_id'] = $userPoint->getCustomerId();
            $customerId = $userPoint->getCustomerId();
            $customer = $this->_customerFactory->create()->load($customerId);
            $point = $userPoint->getPointsChange();
            $rule_id = $userPoint->getRuleId();
            $account = $accountModel;
            $ruleTitle = self::$referercode;
            $this->helper->getSendEmail(null, $account, $point, $rule_id, $ruleTitle, $customer);
            $this->getUpdateData($expiredModel, $userPoint, null);
        }
    }

    /**
     * @param $expiredModel
     * @param $userPoint
     * @param $sendBefore
     */
    public function getUpdateData($expiredModel, $userPoint, $sendBefore)
    {
        $expired = $expiredModel->load($userPoint->getId());
        $checkEmail = $expired->getData();
        if ($sendBefore != null) {
            $checkEmail['check_send_email'] = 'done';
        } else {
            $checkEmail['check_referral'] = 'done';
        }
        $expired->setData($checkEmail)->save();
    }
}
