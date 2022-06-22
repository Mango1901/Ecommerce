<?php

namespace Magenest\AffiliateOpt\Model;

use Magento\Framework\Api\ExtensionAttributesInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magenest\AffiliateOpt\Api\Data\AffiliateCreditmemoItemExtensionInterface;
use Magenest\AffiliateOpt\Api\Data\AffiliateCreditmemoItemInterface;

/**
 * Class AffiliateCreditmemoItem
 * @package Magenest\AffiliateOpt\Model
 */
class AffiliateCreditmemoItem extends AbstractExtensibleModel implements
    AffiliateCreditmemoItemInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\ResourceModel\Order\Creditmemo\Item');
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
     * @param AffiliateCreditmemoItemExtensionInterface $extensionAttributes
     *
     * @return $this
     */
    public function setExtensionAttributes(
        AffiliateCreditmemoItemExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
