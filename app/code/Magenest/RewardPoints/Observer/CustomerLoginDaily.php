<?php

namespace Magenest\RewardPoints\Observer;

use Magenest\RewardPoints\Model\Rule;
use Magenest\RewardPoints\Model\RuleFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\RewardPoints\Helper\Data;

/**
 * Class CustomerLoginDaily
 */
class CustomerLoginDaily implements ObserverInterface
{
    /**
     * @var RuleFactory
     */
    protected $_ruleFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var \Magenest\RewardPoints\Model\ReferralFactory
     */
    protected $referralFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magenest\RewardPoints\Block\Customer\Points
     */
    protected $points;

    /**
     * CustomerRegistration constructor.
     * @param RuleFactory $ruleFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param Data $helper
     * @param \Magenest\RewardPoints\Model\ReferralFactory $referralFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magenest\RewardPoints\Block\Customer\Points $points
     */
    public function __construct(
        RuleFactory $ruleFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        Data $helper,
        \Magenest\RewardPoints\Model\ReferralFactory $referralFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magenest\RewardPoints\Block\Customer\Points $points
    ) {
        $this->_ruleFactory        = $ruleFactory;
        $this->messageManager      = $messageManager;
        $this->_helper             = $helper;
        $this->referralFactory     = $referralFactory;
        $this->customerSession     = $customerSession;
        $this->points              = $points;
    }

    /**
     * @param Observer $observer
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $event      = $observer->getEvent();
        $customer = $event->getCustomer();
        $customerId =  $customer->getEntityId();
        if ($this->_helper->getEnableModule()) {
            if ($customerId != null) {
                $ruleModel = $this->_ruleFactory->create();
                $rules     = $ruleModel->getCollection()->addFieldToFilter('condition', Rule::CONDITION_LOGIN_DAILY);
                if (!empty($rules)) {
                    foreach ($rules as $rule) {
                        $ruleId = $rule->getId();
                        if ($this->_helper->validateRule($rule)) {
                            $point  = $rule->getPoints();
                            $result = $this->_helper->addPoints($customer, $ruleId, null, null);

                            if ($result == true) {
                                $this->messageManager->addSuccessMessage(
                                    sprintf(__('You have earned %s points for login to our store.'), $point)
                                );
                            }
                        }
                    }
                }
            }
        }
    }
}
