<?php

namespace Magenest\RewardPoints\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\RewardPoints\Helper\Data;
use Magenest\RewardPoints\Model\ExpiredFactory;


/**
 * Class CustomerRegistration
 * @package Magenest\RewardPoints\Observer
 */
class CustomerLogin implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var ExpiredFactory
     */
    protected $_expiredFactory;

    /**
     * CustomerLogin constructor.
     * @param ExpiredFactory $expiredFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param Data $helper
     */
    public function __construct(
        ExpiredFactory $expiredFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        Data $helper
    ) {
        $this->_expiredFactory     = $expiredFactory;
        $this->messageManager      = $messageManager;
        $this->_helper             = $helper;
    }

    /**
     * @param Observer $observer
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $event      = $observer->getEvent();
        $now = date("Y-m-d");
        $customer = $observer->getEvent()->getCustomer();
        $customerId =  $customer->getEntityId();
        $sendBeforeNotify = (int)$this->_helper->getSenderBeforeNotify();
        $expiredModel    = $this->_expiredFactory->create();
        $listPointOfUser = $expiredModel->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->setOrder('expired_date', 'ASC');
        $listPointOfUser->getSize();
        if ($this->_helper->getEnableModule()) {
            if ($this->_helper->getSubscribeDefault()) {
                try {
                    if ($listPointOfUser->getSize()) {
                        foreach ($listPointOfUser as $userPoint) {
                            $expiryType = $userPoint->getExpiryType();
                            if ($expiryType === null) {
                                $expiryType = (bool)$sendBeforeNotify;
                            }
                            $timeExpired = $userPoint->getExpiredDate();
                            $dateSendEmail = date('Y-m-d', strtotime($timeExpired . "-" . $sendBeforeNotify . " days"));
                            $point = $userPoint->getPointsChange();
                            $transactionId = $userPoint->getTransactionId();

                            if ($expiryType && $now == $dateSendEmail) {
                                if ($sendBeforeNotify != 0) {
                                    if ($event->getName() === 'customer_login') {
                                        $this->messageManager->addWarning(
                                            sprintf(__('You have %s points about to expire, Id number %s!'), $point,
                                                $transactionId)
                                        );
                                    } else {
                                        $this->messageManager->addWarning(
                                            sprintf(__('You have no points about to expire'))
                                        );
                                    }
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    throw $e;
                }
            }
        }
    }
}
