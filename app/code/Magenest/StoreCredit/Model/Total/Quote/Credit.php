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

namespace Magenest\StoreCredit\Model\Total\Quote;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magenest\StoreCredit\Helper\Calculation;

/**
 * Class Spending
 * @package Magenest\StoreCredit\Model\Total\Quote
 */
class Credit extends AbstractTotal
{
    /**
     * @var Calculation
     */
    protected $helper;

    /**
     * Spending constructor.
     *
     * @param Calculation $helper
     */
    public function __construct(
        Calculation $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     *
     * @return $this
     * @throws LocalizedException
     */
    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        parent::collect($quote, $shippingAssignment, $total);

        $customerId = $this->helper->getCheckoutSession()->getCustomerId() ?: $quote->getCustomerId();
        $session = $this->helper->getCheckoutSession();
        $storeId = $quote->getStoreId();
        $address = $this->_getAddress();

        if (!$address || !$this->helper->isEnabledSpending($storeId, $customerId, $quote)) {
            $quote->setMpStoreCreditSpent(0);
            $quote->setMpStoreCreditBaseDiscount(0);
            $quote->setMpStoreCreditDiscount(0);

            return $this;
        }

        if (($quote->isVirtual() && $address->getAddressType() === Quote\Address::ADDRESS_TYPE_SHIPPING)
            || (!$quote->isVirtual() && $address->getAddressType() === Quote\Address::ADDRESS_TYPE_BILLING)
        ) {
            return $this;
        }

        $customer = $this->helper->getAccountHelper()->getCustomerById($customerId);
        $credit = $this->helper->isAdmin() ? $session->getMpStoreCreditSpent() : $quote->getMpStoreCreditSpent();
        $credit = $credit > 0 ? max($credit, $customer->getMpCreditBalance()) : $credit;
        $discount = $this->getDiscountAmount($quote, $total);

        $baseTotalDiscount = min($credit, $discount, $this->helper->getMaxCreditAmount($quote, $total));
        $totalDiscount = $this->helper->convertPrice($baseTotalDiscount, false, false, $storeId);

        $total->setBaseGrandTotal($total->getBaseGrandTotal() - $baseTotalDiscount);
        $total->setGrandTotal($total->getGrandTotal() - $totalDiscount);

        if ($total->getGrandTotal() < 0) {
            $total->setBaseGrandTotal(0);
            $total->setGrandTotal(0);
        }

        if ($this->helper->isAdmin()) {
            $session->setMpStoreCreditSpent($baseTotalDiscount);
        }
        $quote->setMpStoreCreditSpent($baseTotalDiscount);
        $quote->setMpStoreCreditBaseDiscount($baseTotalDiscount);
        $quote->setMpStoreCreditDiscount($totalDiscount);

        return $this;
    }

    /**
     * Calculate total amount for discount
     *
     * @param Quote $quote
     * @param Total $total
     *
     * @return float
     */
    protected function getDiscountAmount(Quote $quote, Total $total)
    {
        $storeId = $quote->getStoreId();
        $discount = $total->getBaseSubtotal();

        if ($this->helper->isApplyForShipping($storeId)) {
            $discount += $total->getBaseShippingAmount();
        }

        if ($this->helper->isApplyForTax($storeId)) {
            $discount += $total->getBaseTaxAmount();
        }

        /** @var Quote\Item $item */
        foreach ($quote->getAllItems() as $item) {
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                continue;
            }

            $type = $item->getParentItem() ? $item->getParentItem()->getProductType() : $item->getProductType();

            if (!in_array($type, $this->helper->getApplyForProduct($storeId))) {
                $discount -= $item->getBaseRowTotal();
            }
        }

        return $discount;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function fetch(Quote $quote, Total $total)
    {
        $customerId = $this->helper->getCheckoutSession()->getCustomerId() ?: $quote->getCustomerId();
        if (!$this->helper->isEnabledSpending($quote->getStoreId(), $customerId, $quote)) {
            return [];
        }

        $totals = [];

        $credit = $quote->getMpStoreCreditDiscount();
        if ($credit > 0.0001) {
            $totals[] = [
                'code' => $this->getCode(),
                'value' => -$credit,
                'title' => __('Store Credit'),
            ];
        }

        return $totals;
    }
}
