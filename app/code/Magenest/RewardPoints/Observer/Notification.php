<?php

namespace Magenest\RewardPoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class Notification implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Notification constructor.
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magenest\RewardPoints\Helper\Data $helper
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magenest\RewardPoints\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->messageManager = $messageManager;
        $this->helper = $helper;
        $this->customerSession = $customerSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->getEnableModule()) {
            if (!$this->customerSession->isLoggedIn() && $this->helper->isLoginNoficationEnabled()) {
                $this->messageManager->addWarningMessage(__('Please login or create an account to have opportunities to receive bonus points when buying at our store.'));
            }
        }
    }
}
