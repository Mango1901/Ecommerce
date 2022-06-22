<?php


namespace Magenest\Affiliate\Model\Api;

use Magento\Framework\Api\ExtensionAttributesInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magenest\Affiliate\Api\Data\TransactionExtensionInterface;
use Magenest\Affiliate\Api\Data\TransactionInterface;

/**
 * Class Transaction
 * @package Magenest\Affiliate\Model\Api
 */
class Transaction extends AbstractExtensibleModel implements
    TransactionInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\Affiliate\Model\ResourceModel\Transaction');
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionId()
    {
        return $this->getData(self::TRANSACTION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setTransactionId($id)
    {
        return $this->setData(self::TRANSACTION_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccountId()
    {
        return $this->getData(self::ACCOUNT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAccountId($id)
    {
        return $this->setData(self::ACCOUNT_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getHoldingTo()
    {
        return $this->getData(self::HOLDING_TO);
    }

    /**
     * {@inheritdoc}
     */
    public function setHoldingTo($data)
    {
        return $this->setData(self::HOLDING_TO, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($value)
    {
        return $this->setData(self::CUSTOMER_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return $this->getData(self::ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setAction($value)
    {
        return $this->setData(self::ACTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($value)
    {
        return $this->setData(self::TYPE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAmountUsed()
    {
        return $this->getData(self::AMOUNT_USED);
    }

    /**
     * {@inheritdoc}
     */
    public function setAmountUsed($value)
    {
        return $this->setData(self::AMOUNT_USED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentBalance()
    {
        return $this->getData(self::CURRENT_BALANCE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentBalance($value)
    {
        return $this->setData(self::CURRENT_BALANCE, $value);
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
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($value)
    {
        return $this->setData(self::ORDER_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderIncrementId()
    {
        return $this->getData(self::ORDER_INCREMENT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderIncrementId($value)
    {
        return $this->setData(self::ORDER_INCREMENT_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($value)
    {
        return $this->setData(self::STORE_ID, $value);
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
    public function getExtraContent()
    {
        return $this->getData(self::EXTRA_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtraContent($value)
    {
        return $this->setData(self::EXTRA_CONTENT, $value);
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
     * @param TransactionExtensionInterface $extensionAttributes
     *
     * @return $this
     */
    public function setExtensionAttributes(
        TransactionExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
