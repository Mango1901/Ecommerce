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
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Magenest\StoreCredit\Helper\Calculation as Helper;

/**
 * Class ConvertQuoteToOrder
 * @package Magenest\StoreCredit\Observer
 */
class ConvertQuoteToOrder implements ObserverInterface
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * StoreCreditConvertData constructor.
     *
     * @param Helper $helper
     */
    public function __construct(
        Helper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        if ($amount = floatval($quote->getMpStoreCreditBaseDiscount())) {
            $storeCreditDiscount = $quote->getMpStoreCreditDiscount();
            $storeCreditBaseDiscount = $quote->getMpStoreCreditBaseDiscount();
            $order->setMpStoreCreditDiscount($storeCreditDiscount);
            $order->setMpStoreCreditBaseDiscount($storeCreditBaseDiscount);

            if ($customerId = $quote->getCustomerId()) {
                /** Add spending credit transaction */
                $this->helper->addTransaction(Helper::ACTION_SPENDING_ORDER, $customerId, -$amount, $order);
            }
        }

        $storeId = $order->getStoreId();

        $extraContent = [
            'apply_for_product' => $this->helper->getApplyForProduct($storeId),
            'apply_for_tax' => $this->helper->isApplyForTax($storeId),
            'apply_for_shipping' => $this->helper->isApplyForShipping($storeId),
            'allow_refund_product' => $this->helper->isAllowRefundProduct($storeId),
            'allow_refund_exchange' => $this->helper->isAllowRefundExchange($storeId),
            'allow_refund_spending' => $this->helper->isAllowRefundSpending($storeId)
        ];

        $order->setMpStoreCreditExtraContent(Helper::jsonEncode($extraContent));

        return $this;
    }
}
