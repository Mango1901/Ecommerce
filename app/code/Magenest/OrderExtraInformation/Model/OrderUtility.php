<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kangaroo extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\OrderExtraInformation\Model;

use Magento\Sales\Model\Order;
use Magento\Quote\Model\Quote;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class OrderUtility
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    protected $quote;

    protected $quoteResource;

    /**
     * OrderUtility constructor.
     *
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote $quoteResource
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\ResourceModel\Quote $quoteResource
    ) {
        $this->quote = $quoteFactory;
        $this->quoteResource = $quoteResource;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param Order $order
     * @param float $amount
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function updateShippingFee($order, $amount)
    {
        $store = $order->getStore();
        $amountPrice = $this->priceCurrency->convert(
            $amount, $store);
        $order->setBaseShippingFee($amount);
        $order->setShippingFee($amountPrice);

        $order->setBaseGrandTotal($order->getBaseGrandTotal() + $amount);
        $order->setGrandTotal($order->getGrandTotal() + $amountPrice);
        $quoteId = $order->getQuoteId();

        /** @var Quote $quote */
        $quote = $this->quote->create()->load($quoteId);
        if ($quote && $quote->getId()) {
            $quote->collectTotals();
            $this->quoteResource->save($quote);
        }
    }

    /**
     * @param Order $order
     * @return bool
     */
    public static function canChangeShippingFee($order)
    {
        if (!$order instanceof Order) {
            return false;
        }
        $invoices = $order->getInvoiceCollection();
        $shipments = $order->getShipmentsCollection();
//        if (!empty($invoices->getItems()) || !empty($shipments->getItems())) {
//            return false;
//        }

        return true;
    }

    public function formatPrice($price) {
        return $this->priceCurrency->format($price);
    }
}
