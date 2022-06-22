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

namespace Magenest\StoreCredit\Model\Total\Invoice;

use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use Magenest\StoreCredit\Helper\Calculation as Helper;

/**
 * Class Credit
 * @package Magenest\StoreCredit\Model\Total\Invoice
 */
class Credit extends AbstractTotal
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Credit constructor.
     *
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        Helper $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($data);
    }

    /**
     * @param Invoice $invoice
     *
     * @return $this
     */
    public function collect(Invoice $invoice)
    {
        $baseDiscount = $invoice->getBaseSubtotal();
        $discount = $invoice->getSubtotal();

        $order = $invoice->getOrder();

        $extraContent = Helper::jsonDecode($order->getMpStoreCreditExtraContent());

        if (!empty($extraContent['apply_for_shipping'])) {
            $baseDiscount += $invoice->getBaseShippingAmount();
            $discount += $invoice->getShippingAmount();
        }

        if (!empty($extraContent['apply_for_tax'])) {
            $baseDiscount += $invoice->getBaseTaxAmount();
            $discount += $invoice->getTaxAmount();
        }

        $applyForProduct = isset($extraContent['apply_for_product']) ? $extraContent['apply_for_product'] : [];
        /** @var Invoice\Item $item */
        foreach ($invoice->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();

            if ($orderItem->getHasChildren() && $orderItem->isChildrenCalculated()) {
                continue;
            }

            $type = $orderItem->getParentItem() ? $orderItem->getParentItem()->getProductType() : $orderItem->getProductType();

            if (!in_array($type, $applyForProduct)) {
                $baseDiscount -= $item->getBaseRowTotal();
                $discount -= $item->getRowTotal();
            }
        }

        $baseTotalDiscount = min(
            $baseDiscount,
            $order->getMpStoreCreditBaseDiscount() - $this->helper->getAppliedBaseDiscount($invoice)
        );
        $totalDiscount = min(
            $discount,
            $order->getMpStoreCreditDiscount() - $this->helper->getAppliedDiscount($invoice)
        );

        $invoice->setMpStoreCreditBaseDiscount($baseTotalDiscount);
        $invoice->setMpStoreCreditDiscount($totalDiscount);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalDiscount);
        $invoice->setGrandTotal($invoice->getGrandTotal() - $totalDiscount);

        return $this;
    }
}
