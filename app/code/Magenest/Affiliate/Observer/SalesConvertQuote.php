<?php


namespace Magenest\Affiliate\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\Affiliate\Helper\Data as DataHelper;

/**
 * Class SalesConvertQuote
 * @package Magenest\Affiliate\Observer
 */
class SalesConvertQuote implements ObserverInterface
{
    /**
     * @var DataHelper
     */
    protected $_helper;

    /**
     * SalesConvertQuote constructor.
     *
     * @param DataHelper $helper
     */
    public function __construct(DataHelper $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if (!$this->_helper->isEnabled() || !$quote->getAffiliateKey()) {
            return $this;
        }

        $order = $observer->getEvent()->getOrder();
        $order->setAffiliateKey($quote->getAffiliateKey())
            ->setAffiliateEarnCommissionInvoiceAfter($this->_helper->getEarnCommissionInvoiceAfter())
            ->setAffiliateCommission($quote->getAffiliateCommission())
            ->setAffiliateShippingCommission($quote->getAffiliateShippingCommission())
            ->setAffiliateDiscountAmount($quote->getAffiliateDiscountAmount())
            ->setBaseAffiliateDiscountAmount($quote->getBaseAffiliateDiscountAmount())
            ->setBaseAffiliateDiscountShippingAmount($quote->getBaseAffiliateDiscountShippingAmount())
            ->setDiscountCustomerAffiliate($quote->getDiscountCustomerAffiliate())
            ->setBaseDiscountCustomerAffiliate($quote->getBaseDiscountCustomerAffiliate())
            ->setAffiliateDiscountShippingAmount($quote->getAffiliateDiscountShippingAmount())
            ->setAffiliateCommissionFee($this->_helper->getCommissionFee());

        foreach ($order->getItems() as $item) {
            $quoteItem = $quote->getItemById($item->getQuoteItemId());
            if (!$quoteItem) {
                continue;
            }

            $item->setAffiliateDiscountAmount($quoteItem->getAffiliateDiscountAmount())
                ->setBaseAffiliateDiscountAmount($quoteItem->getBaseAffiliateDiscountAmount())
                ->setAffiliateCommission($quoteItem->getAffiliateCommission())
                ->setDiscountCustomerAffiliate($quoteItem->getDiscountCustomerAffiliate())
                ->setBaseDiscountCustomerAffiliate($quoteItem->getBaseDiscountCustomerAffiliate());
        }

        return $this;
    }
}
