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

namespace Magenest\StoreCredit\Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Magenest\StoreCredit\Model\Config\Source\LimitType;
use Magenest\StoreCredit\Model\Config\Source\Scope;

/**
 * Class Calculation
 * @package Magenest\StoreCredit\Helper
 */
class Calculation extends Data
{
    const SPENDING_CONFIGURATION = '/spending';

    /**
     * @param Quote $quote
     * @param Total $total
     *
     * @return float
     */
    public function getMaxCreditAmount($quote, $total)
    {
        $storeId = $quote->getStoreId();
        $limitType = $this->getLimitType($storeId);

        switch ($limitType) {
            case LimitType::FIXED:
                $result = $this->getLimitValue($storeId);
                break;
            case LimitType::PERCENT:
                $result = $this->getLimitValue($storeId) * $total->getBaseSubtotal() / 100;
                break;
            default:
                $result = $total->getBaseSubtotalWithDiscount();
                break;
        }

        if ($this->isApplyForShipping($storeId)) {
            $result += $total->getBaseShippingAmount();
        }

        if ($this->isApplyForTax($storeId)) {
            $result += $total->getBaseTaxAmount();
        }

        return (float)$result;
    }

    /**
     * @param Invoice|Creditmemo $sales
     *
     * @return float
     */
    public function getAppliedDiscount($sales)
    {
        $discount = 0;

        $order = $sales->getOrder();
        if ($sales->getEntityType() === 'invoice') {
            $collection = $order->getInvoiceCollection();
        } else {
            $collection = $order->getCreditmemosCollection();
        }

        foreach ($collection as $item) {
            $discount += $item->getMpStoreCreditDiscount();
        }

        return $discount;
    }

    /**
     * @param Invoice|Creditmemo $sales
     *
     * @return float
     */
    public function getAppliedBaseDiscount($sales)
    {
        $discount = 0;

        $order = $sales->getOrder();
        if ($sales->getEntityType() === 'invoice') {
            $collection = $order->getInvoiceCollection();
        } else {
            $collection = $order->getCreditmemosCollection();
        }

        foreach ($collection as $item) {
            $discount += $item->getMpStoreCreditDiscount();
        }

        $rate = $this->convertPrice(1, false, false, $sales->getStoreId());

        return $discount / $rate;
    }

    /**
     * ======================================= Spending Configuration ==================================================
     *
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigSpending($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . self::SPENDING_CONFIGURATION . $code, $storeId);
    }

    /**
     * @param null $storeId
     * @param null $customerId
     * @param Quote|null $quote
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isEnabledSpending($storeId = null, $customerId = null, $quote = null)
    {
        if (!$this->isEnabled($storeId) || !$customer = $this->getAccountHelper()->getCustomerById($customerId)) {
            return false;
        }

        if (!in_array($customer->getGroupId(), $this->getEnabledForCustomerGroups($storeId))) {
            return false;
        }

        $count = 0;
        $quote = $quote ?: $this->getCheckoutSession()->getQuote();
        $items = $quote->getItems() ?: [];
        /** @var CartItemInterface $item */
        foreach ($items as $item) {
            if ($item->getId() && !in_array($item->getProductType(), $this->getApplyForProduct($storeId))) {
                $count++;
            }
        }

        if ($count && $count == $quote->getItemsCount()) {
            return false;
        }

        if ($this->isAdmin()) {
            return in_array(Scope::ADMIN, $this->getEnabledSpending($storeId));
        }

        return in_array(Scope::FRONTEND, $this->getEnabledSpending($storeId));
    }

    /**
     * @param null $storeId
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isDisabledSpending($storeId = null)
    {
        return !$this->isEnabledSpending($storeId);
    }

    /**
     * @param null $storeId
     *
     * @return array
     */
    public function getEnabledSpending($storeId = null)
    {
        return explode(',', $this->getConfigSpending('enabled', $storeId));
    }

    /**
     * @param null $storeId
     *
     * @return array
     */
    public function getApplyForProduct($storeId = null)
    {
        return explode(',', $this->getConfigSpending('apply_for_product', $storeId));
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isApplyForTax($storeId = null)
    {
        return !!$this->getConfigSpending('apply_for_tax', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isApplyForShipping($storeId = null)
    {
        return !!$this->getConfigSpending('apply_for_shipping', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return int
     */
    public function getLimitType($storeId = null)
    {
        return $this->getConfigSpending('limit_type', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return float
     */
    public function getLimitValue($storeId = null)
    {
        return $this->getConfigSpending('limit_value', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isAllowRefundSpending($storeId = null)
    {
        return !!$this->getConfigSpending('allow_refund_spending', $storeId);
    }
}
