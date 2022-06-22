<?php


namespace Magenest\Affiliate\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\Affiliate\Helper\Calculation;
use Zend_Serializer_Exception;

/**
 * Class InvoiceSaveAfter
 * @package Magenest\Affiliate\Observer
 */
class InvoiceSaveAfter implements ObserverInterface
{
    /**
     * @var Calculation
     */
    protected $calculation;

    /**
     * CreditmemoSaveAfter constructor.
     *
     * @param Calculation $calculation
     */
    public function __construct(
        Calculation $calculation
    ) {
        $this->calculation = $calculation;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     * @throws Zend_Serializer_Exception
     */
    public function execute(Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        if ($order->getAffiliateEarnCommissionInvoiceAfter()) {
            $this->calculation->calculateCommissionOrder($invoice, 'affiliate_commission_invoiced', 'order/invoice');
        }

        return $this;
    }
}
