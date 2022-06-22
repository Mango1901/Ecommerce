<?php


namespace Magenest\Affiliate\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface CampaignInterface
 * @api
 */
interface CampaignInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const CAMPAIGN_ID = 'campaign_id';

    const NAME = 'name';

    const DESCRIPTION = 'description';

    const STATUS = 'status';

    const WEBSITE_IDS = 'website_ids';

    const AFFILIATE_GROUP_IDS = 'affiliate_group_ids';

    const FROM_DATE = 'from_date';

    const TO_DATE = 'to_date';

    const DISPLAY = 'display';

    const SORT_ORDER = 'sort_order';

    const CONDITIONS_SERIALIZED = 'conditions_serialized';

    const ACTIONS_SERIALIZED = 'actions_serialized';

    const COMMISSION = 'commission';

    const DISCOUNT_ACTION = 'discount_action';

    const DISCOUNT_AMOUNT = 'discount_amount';

    const DISCOUNT_QTY = 'discount_qty';

    const DISCOUNT_STEP = 'discount_step';

    const DISCOUNT_DESCRIPTION = 'discount_description';

    const FREE_SHIPPING = 'free_shipping';

    const APPLY_TO_SHIPPING = 'apply_to_shipping';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    const APPLY_DISCOUNT_ON_TAX = 'apply_discount_on_tax';

    const CODE_LENGTH = 'code_length';

    const CODE_FORMAT = 'code_format';

    const COUPON_CODE = 'coupon_code';

    /**#@-*/

    /**
     * @return int
     */
    public function getCampaignId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setCampaignId($value);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setName($value);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDescription($value);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setStatus($value);

    /**
     * @return string
     */
    public function getWebsiteIds();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setWebsiteIds($value);

    /**
     * @return string
     */
    public function getAffiliateGroupIds();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setAffiliateGroupIds($value);

    /**
     * @return string
     */
    public function getFromDate();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setFromDate($value);

    /**
     * @return string
     */
    public function getToDate();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setToDate($value);

    /**
     * @return int
     */
    public function getDisplay();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setDisplay($value);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setSortOrder($value);

    /**
     * @return string
     */
    public function getConditionsSerialized();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setConditionsSerialized($value);

    /**
     * @return string
     */
    public function getActionsSerialized();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setActionsSerialized($value);

    /**
     * @return string
     */
    public function getCommission();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCommission($value);

    /**
     * @return string
     */
    public function getDiscountAction();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDiscountAction($value);

    /**
     * @return float
     */
    public function getDiscountAmount();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setDiscountAmount($value);

    /**
     * @return int
     */
    public function getDiscountQty();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setDiscountQty($value);

    /**
     * @return int
     */
    public function getDiscountStep();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setDiscountStep($value);

    /**
     * @return string
     */
    public function getDiscountDescription();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDiscountDescription($value);

    /**
     * @return int
     */
    public function getFreeShipping();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setFreeShipping($value);

    /**
     * @return int
     */
    public function getApplyToShipping();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setApplyToShipping($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCreatedAt($value);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUpdatedAt($value);

    /**
     * @return int
     */
    public function getApplyDiscountOnTax();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setApplyDiscountOnTax($value);

    /**
     * @return int
     */
    public function getCodeLength();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setCodeLength($value);

    /**
     * @return string
     */
    public function getCodeFormat();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCodeFormat($value);

    /**
     * @return string
     */
    public function getCouponCode();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCouponCode($value);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magenest\Affiliate\Api\Data\CampaignExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magenest\Affiliate\Api\Data\CampaignExtensionInterface $extensionAttributes
     *
     * @return $this
     */
    public function setExtensionAttributes(
        CampaignExtensionInterface $extensionAttributes
    );
}
