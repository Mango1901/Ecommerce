<?php

namespace Magenest\RewardPoints\Observer;

use Magenest\RewardPoints\Model\RuleFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\RewardPoints\Model\AccountFactory;
use Magenest\RewardPoints\Model\TransactionFactory;
use Magenest\RewardPoints\Helper\Data;

/**
 * Class Subscription
 * @package Magenest\RewardPoints\Observer
 */
class Subscription implements ObserverInterface
{
    /**
     * @var AccountFactory
     */
    protected $_accountFactory;

    /**
     * @var TransactionFactory
     */
    protected $_transactionFactory;

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
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Subscription constructor.
     * @param RuleFactory $ruleFactory
     * @param AccountFactory $accountFactory
     * @param Data $helper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param TransactionFactory $transactionFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        RuleFactory $ruleFactory,
        AccountFactory $accountFactory,
        Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        TransactionFactory $transactionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->_ruleFactory        = $ruleFactory;
        $this->_accountFactory     = $accountFactory;
        $this->_transactionFactory = $transactionFactory;
        $this->messageManager      = $messageManager;
        $this->_helper             = $helper;
        $this->_customerFactory = $customerFactory;
    }

    /**
     * @param Observer $observer
     * @return void|null
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $subcriber  = $observer->getEvent()->getSubscriber();
        $customerId = $subcriber->getCustomerId();
        $customer = $this->_customerFactory->create()->load($customerId);
        if ($this->_helper->getEnableModule()) {
            if ($customerId != 0) {
                if ($subcriber->getStatus() == 1 && $subcriber->isStatusChanged() == true) {
                    $ruleModel = $this->_ruleFactory->create();
                    $rules     = $ruleModel->getCollection()->addFieldToFilter('condition','newsletter');
                    if (isset($rules)) {
                        foreach ($rules as $rule) {
                            $ruleId = $rule->getId();
                            if ($this->_helper->validateRule($rule)) {
                                $point = $rule->getPoints();
                                $result = $this->_helper->addPoints($customer, $ruleId, null, null);
                                if ($result == true) {
                                    $this->messageManager->addSuccessMessage(
                                        __('You have earned ') . $point . __(' points for subscribing our newsletter.')
                                    );
                                }
                            }
                        }
                    } else {
                        return null;
                    }
                } elseif ($subcriber->getStatus() == 3 && $subcriber->isStatusChanged() == true) {
                    $ruleModel = $this->_ruleFactory->create();
                    $rules     = $ruleModel->getCollection()->addFieldToFilter('condition','newsletter');
                    if (isset($rules)) {
                        foreach ($rules as $rule) {
                            $ruleId = $rule->getId();
                            if ($this->_helper->validateRule($rule)) {
                                $result = $this->_helper->removePoints($customerId, $ruleId, null, null);
                                if ($result == true) {
                                    $this->messageManager->addSuccessMessage(
                                        __(' Points for subscribing newsletter has been canceled.')
                                    );
                                }
                            }
                        }
                    } else {
                        return null;
                    }
                }
            }
        }
    }
}
