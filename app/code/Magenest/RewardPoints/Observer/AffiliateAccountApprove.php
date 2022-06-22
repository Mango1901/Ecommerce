<?php

namespace Magenest\RewardPoints\Observer;

use Exception;
use Magenest\RewardPoints\Helper\Data;
use Magenest\RewardPoints\Model\ReferralFactory;
use Magenest\RewardPoints\Model\Rule;
use Magenest\RewardPoints\Model\RuleFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class AffiliateAccountApprove
 */
class AffiliateAccountApprove implements ObserverInterface
{
    /**
     * @var RuleFactory
     */
    protected $_ruleFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var ReferralFactory
     */
    protected $referralFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var \Magenest\RewardPoints\Block\Customer\Points
     */
    protected $points;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * CustomerRegistration constructor.
     * @param RuleFactory $ruleFactory
     * @param ManagerInterface $messageManager
     * @param Data $helper
     * @param ReferralFactory $referralFactory
     * @param Session $customerSession
     * @param \Magenest\RewardPoints\Block\Customer\Points $points
     * @param CustomerRepositoryInterface $customerRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        RuleFactory $ruleFactory,
        ManagerInterface $messageManager,
        Data $helper,
        ReferralFactory $referralFactory,
        Session $customerSession,
        \Magenest\RewardPoints\Block\Customer\Points $points,
        CustomerRepositoryInterface $customerRepository,
        LoggerInterface $logger
    ) {
        $this->_ruleFactory = $ruleFactory;
        $this->messageManager = $messageManager;
        $this->_helper = $helper;
        $this->referralFactory = $referralFactory;
        $this->customerSession = $customerSession;
        $this->points = $points;
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        try {
            $affiliateAccount = $event->getObject();
            $customerId = $affiliateAccount->getCustomerId();
            $customer = $this->customerRepository->getById($customerId);
            if ($this->_helper->getEnableModule()) {
                if ($customerId != null) {
                    $ruleModel = $this->_ruleFactory->create();
                    $rules = $ruleModel->getCollection()->addFieldToFilter('condition', Rule::CONDITION_REGISTRATION_AFFILIATE);
                    if (!empty($rules)) {
                        foreach ($rules as $rule) {
                            $ruleId = $rule->getId();
                            if ($this->_helper->validateRule($rule)) {
                                $point = $rule->getPoints();
                                $result = $this->_helper->addPoints($customer, $ruleId, null, null);

                                if ($result == true) {
                                    $this->messageManager->addSuccessMessage(
                                        sprintf(__('Affiliate account with id %s have earned %s points for registration successfully program.'), $affiliateAccount->getId(), $point)
                                    );
                                }

                                $recipients = [
                                    'name' => $customer->getLastname() . ' ' . $customer->getFirstname(),
                                    'email' => $customer->getEmail()
                                ];
                                $this->_helper->sendCouponForAffiliateAndBirthday(
                                    $recipients,
                                    Data::XML_PATH_AFFILIATE_APPLY_COUPON_AFFILIATE,
                                    Data::XML_PATH_AFFILIATE_SHOPPING_CART_RULE_AFFILIATE,
                                    Data::XML_PATH_AFFILIATE_EMAIL_TEMPLATE_AFFILIATE
                                );
                            }
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
