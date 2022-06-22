<?php


namespace Magenest\Affiliate\Model\Api;

use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Api\ExtensionAttributesInterface;
use Magenest\Affiliate\Api\Data\CampaignInterface;
use Magenest\Affiliate\Api\Data\CampaignExtensionInterface;

/**
 * Class Campaign
 * @package Magenest\Affiliate\Model\Api
 */
class Campaign extends AbstractExtensibleModel implements
    CampaignInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\Affiliate\Model\ResourceModel\Campaign');
    }

    /**
     * {@inheritdoc}
     */
    public function getCampaignId()
    {
        return $this->getData(self::CAMPAIGN_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCampaignId($value)
    {
        return $this->setData(self::CAMPAIGN_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($value)
    {
        return $this->setData(self::DESCRIPTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsiteIds()
    {
        return $this->getData(self::WEBSITE_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsiteIds($value)
    {
        return $this->setData(self::WEBSITE_IDS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAffiliateGroupIds()
    {
        return $this->getData(self::AFFILIATE_GROUP_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAffiliateGroupIds($value)
    {
        return $this->setData(self::AFFILIATE_GROUP_IDS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFromDate()
    {
        return $this->getData(self::FROM_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFromDate($value)
    {
        return $this->setData(self::FROM_DATE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getToDate()
    {
        return $this->getData(self::TO_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setToDate($value)
    {
        return $this->setData(self::TO_DATE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplay()
    {
        return $this->getData(self::DISPLAY);
    }

    /**
     * {@inheritdoc}
     */
    public function setDisplay($value)
    {
        return $this->setData(self::DISPLAY, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setSortOrder($value)
    {
        return $this->setData(self::SORT_ORDER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionsSerialized()
    {
        return $this->getData(self::CONDITIONS_SERIALIZED);
    }

    /**
     * {@inheritdoc}
     */
    public function setConditionsSerialized($value)
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getActionsSerialized()
    {
        return $this->getData(self::ACTIONS_SERIALIZED);
    }

    /**
     * {@inheritdoc}
     */
    public function setActionsSerialized($value)
    {
        return $this->setData(self::ACTIONS_SERIALIZED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCommission()
    {
        return $this->getData(self::COMMISSION);
    }

    /**
     * {@inheritdoc}
     */
    public function setCommission($value)
    {
        return $this->setData(self::COMMISSION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountAction()
    {
        return $this->getData(self::DISCOUNT_ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDiscountAction($value)
    {
        return $this->setData(self::DISCOUNT_ACTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountAmount()
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setDiscountAmount($value)
    {
        return $this->setData(self::DISCOUNT_AMOUNT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountQty()
    {
        return $this->getData(self::DISCOUNT_QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setDiscountQty($value)
    {
        return $this->setData(self::DISCOUNT_QTY, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountStep()
    {
        return $this->getData(self::DISCOUNT_STEP);
    }

    /**
     * {@inheritdoc}
     */
    public function setDiscountStep($value)
    {
        return $this->setData(self::DISCOUNT_STEP, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountDescription()
    {
        return $this->getData(self::DISCOUNT_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDiscountDescription($value)
    {
        return $this->setData(self::DISCOUNT_DESCRIPTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFreeShipping()
    {
        return $this->getData(self::FREE_SHIPPING);
    }

    /**
     * {@inheritdoc}
     */
    public function setFreeShipping($value)
    {
        return $this->setData(self::FREE_SHIPPING, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getApplyToShipping()
    {
        return $this->getData(self::APPLY_TO_SHIPPING);
    }

    /**
     * {@inheritdoc}
     */
    public function setApplyToShipping($value)
    {
        return $this->setData(self::APPLY_TO_SHIPPING, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($value)
    {
        return $this->setData(self::UPDATED_AT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getApplyDiscountOnTax()
    {
        return $this->getData(self::APPLY_DISCOUNT_ON_TAX);
    }

    /**
     * {@inheritdoc}
     */
    public function setApplyDiscountOnTax($value)
    {
        return $this->setData(self::APPLY_DISCOUNT_ON_TAX, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeLength()
    {
        return $this->getData(self::CODE_LENGTH);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodeLength($value)
    {
        return $this->setData(self::CODE_LENGTH, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeFormat()
    {
        return $this->getData(self::CODE_FORMAT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodeFormat($value)
    {
        return $this->setData(self::CODE_FORMAT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponCode()
    {
        return $this->getData(self::COUPON_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponCode($value)
    {
        return $this->setData(self::COUPON_CODE, $value);
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
     * @param CampaignExtensionInterface $extensionAttributes
     *
     * @return $this
     */
    public function setExtensionAttributes(
        CampaignExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
