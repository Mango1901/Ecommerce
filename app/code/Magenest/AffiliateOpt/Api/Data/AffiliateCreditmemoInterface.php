<?php

namespace Magenest\AffiliateOpt\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface AffiliateCreditmemoInterface
 * @api
 */
interface AffiliateCreditmemoInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const AFFILIATE_COMMISSION = 'affiliate_commission';

    const AFFILIATE_DISCOUNT_AMOUNT = 'affiliate_discount_amount';

    const BASE_AFFILIATE_DISCOUNT_AMOUNT = 'base_affiliate_discount_amount';

    /**#@-*/

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
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magenest\AffiliateOpt\Api\Data\AffiliateCreditmemoExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magenest\AffiliateOpt\Api\Data\AffiliateCreditmemoExtensionInterface $extensionAttributes
     *
     * @return $this
     */
    public function setExtensionAttributes(
        \Magenest\AffiliateOpt\Api\Data\AffiliateCreditmemoExtensionInterface $extensionAttributes
    );
}
