<?php


namespace Magenest\Affiliate\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Payment\Model\Cart;

/**
 * Class PaypalPrepareItems
 * @package Magenest\Affiliate\Observer
 */
class PaypalPrepareItems implements ObserverInterface
{
    /**
     * Add affiliate amount to payment discount total
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Cart $cart */
        $cart = $observer->getEvent()->getCart();
        $salesEntity = $cart->getSalesModel();
        $discount = abs($salesEntity->getDataUsingMethod('base_affiliate_discount_amount'));
        if ($discount > 0.0001) {
            $cart->addCustomItem('Affiliate Discount', 1, -1.00 * $discount);
        }
    }
}
