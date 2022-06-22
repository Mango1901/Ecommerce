<?php

namespace Magenest\AffiliateOpt\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface AffiliateInterface
 * @api
 */
interface AffiliateInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const AFFILIATE_KEY = 'affiliate_key';

    const AFFILIATE_COMMISSION = 'affiliate_commission';

    const AFFILIATE_DISCOUNT_AMOUNT = 'affiliate_discount_amount';

    const BASE_AFFILIATE_DISCOUNT_AMOUNT = 'base_affiliate_discount_amount';

    const AFFILIATE_SHIPPING_COMMISSION = 'affiliate_shipping_commission';

    const AFFILIATE_EARN_COMMISSION_INVOICE_AFTER = 'affiliate_earn_commission_invoice_after';

    const AFFILIATE_DISCOUNT_SHIPPING_AMOUNT = 'affiliate_discount_shipping_amount';

    const AFFILIATE_BASE_DISCOUNT_SHIPPING_AMOUNT = 'affiliate_base_discount_shipping_amount';

    const AFFILIATE_DISCOUNT_INVOICED = 'affiliate_discount_invoiced';

    const BASE_AFFILIATE_DISCOUNT_INVOICED = 'base_affiliate_discount_invoiced';

    const AFFILIATE_COMMISSION_INVOICED = 'affiliate_commission_invoiced';

    const AFFILIATE_DISCOUNT_SHIPPING_INVOICED = 'affiliate_discount_shipping_invoiced';

    const AFFILIATE_DISCOUNT_REFUNDED = 'affiliate_discount_refunded';

    const BASE_AFFILIATE_DISCOUNT_REFUNDED = 'base_affiliate_discount_refunded';

    const AFFILIATE_COMMISSION_REFUNDED = 'affiliate_commission_refunded';

    const AFFILIATE_COMMISSION_HOLDING_REFUNDED = 'affiliate_commission_holding_refunded';

    const AFFILIATE_DISCOUNT_SHIPPING_REFUNDED = 'affiliate_discount_shipping_refunded';

    /**#@-*/

    /**
     * Return the affiliate key.
     *
     * @return int affiliate key.
     */
    public function getAffiliateKey();

    /**
     * Set the affiliate key.
     *
     * @param string|null $key
     *
     * @return $this
     */
    public function setAffiliateKey($key);

    /**
     * Return the affiliate commission.
     *
     * @return string affiliate commission.
     */
    public function getAffiliateCommission();

    /**
     * Set affiliate commission.
     *
     * @param string|null $commission
     *
     * @return $this
     */
    public function setAffiliateCommission($commission);

    /**
     * Return the affiliate discount amount.
     *
     * @return string
     */
    public function getAffiliateDiscountAmount();

    /**
     * Set the affiliate discount amount.
     *
     * @param string $amount
     *
     * @return $this
     */
    public function setAffiliateDiscountAmount($amount);

    /**
     * Return the  base affiliate discount amount.
     *
     * @return string
     */
    public function getBaseAffiliateDiscountAmount();

    /**
     * Set the base affiliate discount amount.
     *
     * @param string $amount
     *
     * @return $this
     */
    public function setBaseAffiliateDiscountAmount($amount);

    /**
     * Return the affiliate_shipping_commission.
     *
     * @return string
     */
    public function getAffiliateShippingCommission();

    /**
     * @param string $shippingCommission
     *
     * @return $this
     */
    public function setAffiliateShippingCommission($shippingCommission);

    /**
     * @return string
     */
    public function getAffiliateEarnCommissionInvoiceAfter();

    /**
     * @param string $commissionInvoiceAfter
     *
     * @return $this
     */
    public function setAffiliateEarnCommissionInvoiceAfter($commissionInvoiceAfter);

    /**
     * @return string
     */
    public function getAffiliateDiscountShippingAmount();

    /**
     * @param string $amount
     *
     * @return $this
     */
    public function setAffiliateDiscountShippingAmount($amount);

    /**
     * @return string
     */
    public function getBaseAffiliateDiscountShippingAmount();

    /**
     * @param string $amount
     *
     * @return $this
     */
    public function setBaseAffiliateDiscountShippingAmount($amount);

    /**
     * @return string
     */
    public function getAffiliateDiscountInvoiced();

    /**
     * @param string $amount
     *
     * @return $this
     */
    public function setAffiliateDiscountInvoiced($amount);

    /**
     * @return string
     */
    public function getBaseAffiliateDiscountInvoiced();

    /**
     * @param string $amount
     *
     * @return $this
     */
    public function setBaseAffiliateDiscountInvoiced($amount);

    /**
     * @return string
     */
    public function getAffiliateCommissionInvoiced();

    /**
     * @param string $amount
     *
     * @return $this
     */
    public function setAffiliateCommissionInvoiced($amount);

    /**
     * @return string
     */
    public function getAffiliateDiscountShippingInvoiced();

    /**
     * @param string $amount
     *
     * @return $this
     */
    public function setAffiliateDiscountShippingInvoiced($amount);

    /**
     * @return string
     */
    public function getAffiliateDiscountRefunded();

    /**
     * @param string $amount
     *
     * @return $this
     */
    public function setAffiliateDiscountRefunded($amount);

    /**
     * @return string
     */
    public function getBaseAffiliateDiscountRefunded();

    /**
     * @param string $amount
     *
     * @return $this
     */
    public function setBaseAffiliateDiscountRefunded($amount);

    /**
     * @return string
     */
    public function getAffiliateCommissionRefunded();

    /**
     * @param string $amount
     *
     * @return $this
     */
    public function setAffiliateCommissionRefunded($amount);

    /**
     * @return string
     */
    public function getAffiliateCommissionHoldingRefunded();

    /**
     * @param string $amount
     *
     * @return $this
     */
    public function setAffiliateCommissionHoldingRefunded($amount);

    /**
     * @return string
     */
    public function getAffiliateDiscountShippingRefunded();

    /**
     * @param string $amount
     *
     * @return $this
     */
    public function setAffiliateDiscountShippingRefunded($amount);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magenest\AffiliateOpt\Api\Data\AffiliateExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magenest\AffiliateOpt\Api\Data\AffiliateExtensionInterface $extensionAttributes
     *
     * @return $this
     */
    public function setExtensionAttributes(
        \Magenest\AffiliateOpt\Api\Data\AffiliateExtensionInterface $extensionAttributes
    );
}
