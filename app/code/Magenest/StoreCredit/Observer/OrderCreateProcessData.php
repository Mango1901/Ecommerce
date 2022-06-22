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

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magenest\StoreCredit\Helper\Data;
use Magenest\StoreCredit\Model\Customer;

/**
 * Class OrderCreateProcessData
 * @package Magenest\StoreCredit\Observer
 */
class OrderCreateProcessData implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * OrderCreateProcessData constructor.
     *
     * @param Data $helper
     * @param Customer $customer
     */
    public function __construct(Data $helper, Customer $customer)
    {
        $this->helper = $helper;
        $this->customer = $customer;
    }

    /**
     * Process post data and set usage of Store Credit into order creation model
     *
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var Quote $quote */
        $quote = $observer->getEvent()->getOrderCreateModel()->getQuote();
        $data = $observer->getEvent()->getRequest();

        $customerId = $quote->getCustomerId();
        $customerCreditAmount = $this->customer->load($customerId)->getMpCreditBalance();

        if (isset($data['mp_spending_credit'])) {
            $baseGrandTotal = $quote->getBaseGrandTotal();
            $amount = ($customerCreditAmount < $baseGrandTotal) ? $customerCreditAmount : $baseGrandTotal;
            $value = $data['mp_spending_credit'] == 'true' ? $amount : 0;
            $this->helper->getCheckoutSession()->setMpStoreCreditSpent($value);
        }

        return $this;
    }
}
