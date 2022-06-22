<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Observer;

use Exception;
use Magento\Customer\Model\Data\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\StoreCredit\Helper\Email;
use Magenest\StoreCredit\Model\CustomerFactory;

/**
 * Class CustomerSaveAfter
 * @package Magenest\StoreCredit\Observer
 */
class CustomerSaveAfter implements ObserverInterface
{
    /**
     * @var Email
     */
    protected $helper;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * CustomerSaveAfter constructor.
     *
     * @param CustomerFactory $customerFactory
     * @param Email $helper
     */
    public function __construct(CustomerFactory $customerFactory, Email $helper)
    {
        $this->customerFactory = $customerFactory;
        $this->helper = $helper;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Customer $customer */
        $customer = $observer->getEvent()->getCustomer();
        $request = $observer->getEvent()->getRequest();

        $data = $request ?
            $request->getPost('mpstorecredit') :
            ['mp_credit_notification' => $this->helper->isSubscribeByDefault($customer->getStoreId())];

        $customerModel = $this->customerFactory->create();
        $customerModel->load($customer->getId());
        $customerModel->saveAttributeData($customer->getId(), $data ?: []);

        return $this;
    }
}
