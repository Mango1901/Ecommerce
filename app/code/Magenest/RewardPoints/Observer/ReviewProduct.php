<?php

namespace Magenest\RewardPoints\Observer;

use Magenest\RewardPoints\Model\RuleFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\RewardPoints\Model\AccountFactory;
use Magenest\RewardPoints\Model\TransactionFactory;
use Magenest\RewardPoints\Helper\Data;

/**
 * Class ReviewProduct
 * @package Magenest\RewardPoints\Observer
 */
class ReviewProduct implements ObserverInterface
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
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * ReviewProduct constructor.
     * @param RuleFactory $ruleFactory
     * @param AccountFactory $accountFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param Data $helper
     * @param TransactionFactory $transactionFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        RuleFactory $ruleFactory,
        AccountFactory $accountFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        Data $helper,
        TransactionFactory $transactionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->_ruleFactory        = $ruleFactory;
        $this->_accountFactory     = $accountFactory;
        $this->_transactionFactory = $transactionFactory;
        $this->messageManager      = $messageManager;
        $this->_helper             = $helper;
        $this->_reviewFactory      = $reviewFactory;
        $this->_customerFactory = $customerFactory;
    }

    /**
     * @param Observer $observer
     *
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $review     = $observer->getObject();
        $productId  = $review->getdata('entity_pk_value');
        $oldStatus  = $review->getOrigData('status_id');
        $newStatus  = $review->getData('status_id');
        $customerId = $review->getData('customer_id');
        $customer = $this->_customerFactory->create()->load($customerId);
        if ($this->_helper->getEnableModule()) {
            if ($customerId != null) {
                $ruleModel = $this->_ruleFactory->create();
                $rules     = $ruleModel->getCollection()->addFieldToFilter('condition','review');
                if (!empty($rules)) {
                    foreach ($rules as $rule) {
                        $ruleId = $rule->getId();
                        if ($this->_helper->validateRule($rule) == true) {
                            $point = $rule->getPoints();
                            if ($newStatus != $oldStatus && $newStatus == 1) {
                                $this->_helper->addPoints($customer, $ruleId, null, $productId);
                            } elseif ($oldStatus == null) {
                                $this->messageManager->addSuccessMessage(
                                    __('You will earn ') . $point . __(' points when store admin approves your review.')
                                );
                            }
                        }
                    }
                }
            } else {
                $this->messageManager->addErrorMessage(
                    (__('You need to login to receive reward points for reviewing this product.'))
                );
            }
        }
    }
}
