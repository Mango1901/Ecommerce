<?php

namespace Magenest\PaymentEPay\Observer;

use Magento\Framework\Event\ObserverInterface;

class PaymentMethodAvailable implements ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        $this->checkoutSession = $checkoutSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $count = 0;
        $method = $observer->getMethodInstance();
        if ($observer->getQuote() != null) {
            $quote = $observer->getQuote()->getItems();
            if (!empty($quote)) {
                foreach ($quote as $one) {
                    $count++;
                }
            }
        }

        if ($count > 1) {
            if ($method->getCode() == 'vnpt_epay_is') {
                $checkResult = $observer->getEvent()->getResult();
                $checkResult->setData('is_available', false);
            }
        } else {
            if (!empty($this->checkoutSession->getInstallmentPaymentValue())) {
                if ($method->getCode() != 'vnpt_epay_is') {
                    $checkResult = $observer->getEvent()->getResult();
                    $checkResult->setData('is_available', false);
                }
            } else {
                if ($method->getCode() == 'vnpt_epay_is') {
                    $checkResult = $observer->getEvent()->getResult();
                    $checkResult->setData('is_available', false);
                }
            }
        }

    }
}
