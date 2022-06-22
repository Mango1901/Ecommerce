<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\OrderExtraInformation\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;

/**
 * Class SalesConvertQuote
 *
 * @package Magenest\OrderExtraInformation\Observer
 */
class SalesConvertQuote implements ObserverInterface
{
    /**
     * @var CustomerRepository
     */
    protected $_customerRepository;

    /**
     * Constructor.
     *
     * @param CustomerRepository $customerRepository
     */
    function __construct(
        CustomerRepository $customerRepository
    ) {
        $this->_customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        $order->setCustomerNote($quote->getCustomerNote());
//        $order->setDeliveryDate($quote->getDeliveryDate());
        $order->setDeliveryTime($quote->getDeliveryTime());
        $order->setCompanyName($quote->getCompanyName());
        $order->setTaxCode($quote->getTaxCode());
        $order->setCompanyAddress($quote->getCompanyAddress());
        $order->setCompanyEmail($quote->getCompanyEmail());
        $order->setIsWholesaleOrder($quote->getIsWholesaleOrder());


        if ($telephoneCustomerConsign = $quote->getTelephoneCustomerConsign()) {
            $order->setTelephoneCustomerConsign($telephoneCustomerConsign);
        }

        if ($quote->getCustomerId() && $order->getCompanyName()) {
            $customer = $this->_customerRepository->getById($quote->getCustomerId());
            $vatInvoice = $customer->getCustomAttribute('default_vat_invoice');
            if (!$vatInvoice || !$vatInvoice->getValue()) {
                $customer->setCustomAttribute(
                    'default_vat_invoice', \Zend_Json::encode(
                    [
                        'company_name' => $order->getCompanyName(),
                        'tax_code' => $order->getTaxCode(),
                        'company_address' => $order->getCompanyAddress(),
                        'company_email' => $order->getCompanyEmail(),
                        'delivery_time' => $order->getDeliveryTime(),
                        'telephone_customer_consign' => $order->getTelephoneCustomerConsign()
                    ]));
                $customer->setData('telephone', $order->getShippingAddress()->getTelephone());
                $this->_customerRepository->save($customer);
            }
        }

        foreach ($order->getItems() as $orderItem) {
            $quoteItem = $quote->getItemById($orderItem->getQuoteItemId());
            if ($quoteItem && $quoteItem->hasData('customer_note')) {
                $orderItem->setCustomerNote($quoteItem->getCustomerNote());
            }
        }

        return $this;
    }
}
