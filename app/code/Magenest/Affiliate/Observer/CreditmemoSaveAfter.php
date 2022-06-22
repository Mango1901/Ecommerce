<?php


namespace Magenest\Affiliate\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magenest\Affiliate\Helper\Calculation;
use Zend_Serializer_Exception;

/**
 * Class CreditmemoSaveAfter
 * @package Magenest\Affiliate\Observer
 */
class CreditmemoSaveAfter implements ObserverInterface
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
     * @param $order
     *
     * @return bool
     */
    public function canRefundCommission($order)
    {
        $isEarnCommissionInvoice = $order->getAffiliateEarnCommissionInvoiceAfter();

        return $this->calculation->isProcessRefund($order->getStoreId())
            && ($isEarnCommissionInvoice
                || (!$isEarnCommissionInvoice
                    && ($order->getState() == Order::STATE_COMPLETE || $order->getState() == Order::STATE_CLOSED)));
    }

    /**
     * @param EventObserver $observer
     *
     * @return $this
     * @throws Zend_Serializer_Exception
     */
    public function execute(EventObserver $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();
        if (!$this->canRefundCommission($order)) {
            return $this;
        }
        $this->calculation->calculateCommissionOrder($creditmemo, 'affiliate_commission_refunded', 'order/refund');

        return $this;
    }
}
