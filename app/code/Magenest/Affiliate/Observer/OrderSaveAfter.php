<?php

namespace Magenest\Affiliate\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magenest\Affiliate\Helper\Calculation;
use Magenest\Affiliate\Model\TransactionFactory;
use Zend_Serializer_Exception;
use Magento\Quote\Model\QuoteFactory;

/**
 * Class OrderSaveAfter
 *
 * @package Magenest\Affiliate\Observer
 */
class OrderSaveAfter implements ObserverInterface
{
    /**
     * @var TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @var Calculation
     */
    protected $calculation;

    /**
     * @var QuoteFactory
     */
    private $quote;

    /**
     * OrderSaveAfter constructor.
     *
     * @param TransactionFactory $transactionFactory
     * @param Calculation $calculation
     * @param QuoteFactory $quote
     */
    public function __construct(
        TransactionFactory $transactionFactory,
        Calculation $calculation,
        QuoteFactory $quote
    ) {
        $this->_transactionFactory = $transactionFactory;
        $this->calculation         = $calculation;
        $this->quote         = $quote;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     * @throws Zend_Serializer_Exception
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if (!$order->getAffiliateEarnCommissionInvoiceAfter()
            && $order->getState() == Order::STATE_COMPLETE
            && $order->getAffiliateCommission()) {
            $transaction = $this->_transactionFactory->create()->getCollection()
                ->addFieldToFilter('action', 'order/invoice')
                ->addFieldToFilter('order_id', $order->getId())
                ->getFirstItem();
            if ($transaction->getId()) {
                return $this;
            }

            $totalTierCommission = [];
            if ($order->hasCreditmemos()) {
                foreach ($order->getItems() as $item) {
                    $qty            = $item->getQtyOrdered() - $item->getQtyRefunded();
                    $itemCommission = $this->calculation->unserialize($item->getAffiliateCommission());
                    if ($itemCommission && $qty > 0) {
                        $totalItemCommission = $this->calculation->getTotalTierCommission($itemCommission);
                        $this->calculation->setTotalTierCommission(
                            $totalTierCommission,
                            $totalItemCommission,
                            $qty / $item->getQtyOrdered()
                        );
                    }
                }
                $shippingCommission = $this->calculation->parseCommissionOnTier(
                    $order->getAffiliateShippingCommission()
                );
                $this->calculation->setTotalTierCommission($totalTierCommission, $shippingCommission);
            } else {
                $commission          = $this->calculation->unserialize($order->getAffiliateCommission());
                $totalTierCommission = $this->calculation->getTotalTierCommission($commission);
            }

            $this->calculation->createTransactionByAction($order, $totalTierCommission, 'order/invoice');
        }

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
