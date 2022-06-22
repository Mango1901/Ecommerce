<?php

namespace Magenest\AffiliateOpt\Model;

use Magento\Framework\Api\ExtensionAttributesInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magenest\AffiliateOpt\Api\Data\AffiliateExtensionInterface;
use Magenest\AffiliateOpt\Api\Data\AffiliateInterface;

/**
 * Class Affiliate
 * @package Magenest\AffiliateOpt\Model
 */
class Affiliate extends AbstractExtensibleModel implements
    AffiliateInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\ResourceModel\Order');
    }

    /**
     * {@inheritdoc}
     */
    public function getAffiliateKey()
    {
        return $this->getData(self::AFFILIATE_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setAffiliateKey($key)
    {
        return $this->setData(self::AFFILIATE_KEY, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function getAffiliateCommission()
    {
        return $this->getData(self::AFFILIATE_COMMISSION);
    }

    /**
     * {@inheritdoc}
     */
    public function setAffiliateCommission($commission)
    {
        return $this->setData(self::AFFILIATE_COMMISSION, $commission);
    }

    /**
     * {@inheritdoc}
     */
    public function getAffiliateDiscountAmount()
    {
        return $this->getData(self::AFFILIATE_DISCOUNT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAffiliateDiscountAmount($amount)
    {
        return $this->setData(self::AFFILIATE_DISCOUNT_AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAffiliateDiscountAmount()
    {
        return $this->getData(self::BASE_AFFILIATE_DISCOUNT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseAffiliateDiscountAmount($amount)
    {
        return $this->setData(self::BASE_AFFILIATE_DISCOUNT_AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getAffiliateShippingCommission()
    {
        return $this->getData(self::AFFILIATE_SHIPPING_COMMISSION);
    }

    /**
     * {@inheritdoc}
     */
    public function setAffiliateShippingCommission($shippingCommission)
    {
        return $this->setData(self::AFFILIATE_SHIPPING_COMMISSION, $shippingCommission);
    }

    /**
     * {@inheritdoc}
     */
    public function getAffiliateEarnCommissionInvoiceAfter()
    {
        return $this->getData(self::AFFILIATE_EARN_COMMISSION_INVOICE_AFTER);
    }

    /**
     * {@inheritdoc}
     */
    public function setAffiliateEarnCommissionInvoiceAfter($commissionInvoiceAfter)
    {
        return $this->setData(self::AFFILIATE_EARN_COMMISSION_INVOICE_AFTER, $commissionInvoiceAfter);
    }

    /**
     * {@inheritdoc}
     */
    public function getAffiliateDiscountShippingAmount()
    {
        return $this->getData(self::AFFILIATE_DISCOUNT_SHIPPING_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAffiliateDiscountShippingAmount($amount)
    {
        return $this->setData(self::AFFILIATE_DISCOUNT_SHIPPING_AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAffiliateDiscountShippingAmount()
    {
        return $this->getData(self::AFFILIATE_BASE_DISCOUNT_SHIPPING_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseAffiliateDiscountShippingAmount($amount)
    {
        return $this->setData(self::AFFILIATE_BASE_DISCOUNT_SHIPPING_AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getAffiliateDiscountInvoiced()
    {
        return $this->getData(self::AFFILIATE_DISCOUNT_INVOICED);
    }

    /**
     * {@inheritdoc}
     */
    public function setAffiliateDiscountInvoiced($amount)
    {
        return $this->setData(self::AFFILIATE_DISCOUNT_INVOICED, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAffiliateDiscountInvoiced()
    {
        return $this->getData(self::BASE_AFFILIATE_DISCOUNT_INVOICED);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseAffiliateDiscountInvoiced($amount)
    {
        return $this->setData(self::BASE_AFFILIATE_DISCOUNT_INVOICED, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getAffiliateCommissionInvoiced()
    {
        return $this->getData(self::AFFILIATE_COMMISSION_INVOICED);
    }

    /**
     * {@inheritdoc}
     */
    public function setAffiliateCommissionInvoiced($amount)
    {
        return $this->setData(self::AFFILIATE_COMMISSION_INVOICED, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getAffiliateDiscountShippingInvoiced()
    {
        return $this->getData(self::AFFILIATE_DISCOUNT_SHIPPING_INVOICED);
    }

    /**
     * {@inheritdoc}
     */
    public function setAffiliateDiscountShippingInvoiced($amount)
    {
        return $this->setData(self::AFFILIATE_DISCOUNT_SHIPPING_INVOICED, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getAffiliateDiscountRefunded()
    {
        return $this->getData(self::AFFILIATE_DISCOUNT_REFUNDED);
    }

    /**
     * {@inheritdoc}
     */
    public function setAffiliateDiscountRefunded($amount)
    {
        return $this->setData(self::AFFILIATE_DISCOUNT_REFUNDED, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAffiliateDiscountRefunded()
    {
        return $this->getData(self::BASE_AFFILIATE_DISCOUNT_REFUNDED);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseAffiliateDiscountRefunded($amount)
    {
        return $this->setData(self::BASE_AFFILIATE_DISCOUNT_REFUNDED, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getAffiliateCommissionRefunded()
    {
        return $this->getData(self::AFFILIATE_COMMISSION_REFUNDED);
    }

    /**
     * {@inheritdoc}
     */
    public function setAffiliateCommissionRefunded($amount)
    {
        return $this->setData(self::AFFILIATE_COMMISSION_REFUNDED, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getAffiliateCommissionHoldingRefunded()
    {
        return $this->getData(self::AFFILIATE_COMMISSION_HOLDING_REFUNDED);
    }

    /**
     * {@inheritdoc}
     */
    public function setAffiliateCommissionHoldingRefunded($amount)
    {
        return $this->setData(self::AFFILIATE_COMMISSION_HOLDING_REFUNDED, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getAffiliateDiscountShippingRefunded()
    {
        return $this->getData(self::AFFILIATE_DISCOUNT_SHIPPING_REFUNDED);
    }

    /**
     * {@inheritdoc}
     */
    public function setAffiliateDiscountShippingRefunded($amount)
    {
        return $this->setData(self::AFFILIATE_DISCOUNT_SHIPPING_REFUNDED, $amount);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return ExtensionAttributesInterface
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     *
     * @param AffiliateExtensionInterface $extensionAttributes
     *
     * @return $this
     */
    public function setExtensionAttributes(
        AffiliateExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
