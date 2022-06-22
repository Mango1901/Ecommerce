<?php

namespace Magenest\AffiliateOpt\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Model\Order;
use Zend_Serializer_Exception;

/**
 * Class OrderSaveAfter
 * @package Magenest\AffiliateOpt\Observer
 */
class OrderSaveAfter implements ObserverInterface
{
    /**
     * @var QuoteFactory
     */
    protected $quote;

    /**
     * OrderSaveAfter constructor.
     *
     * @param QuoteFactory $quote
     */
    public function __construct(
        QuoteFactory $quote
    ) {
        $this->quote = $quote;
    }

    /**
     * @param Observer $observer
     *
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $status = $order->getState();
        if ($status === Order::STATE_CANCELED || $status === Order::STATE_CLOSED) {
            $quote = $this->quote->create()->load($order->getQuoteId());
            foreach ($order->getItems() as $item) {
                $quoteItem = $quote->getItemById($item->getQuoteItemId());
                if (!$quoteItem) {
                    continue;
                }
                $item->setAffiliateCommission(0);
            }
        }

        return $this;
    }
}
