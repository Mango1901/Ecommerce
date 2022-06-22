<?php


namespace Magenest\Affiliate\Model\Api;

use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Api\ExtensionAttributesInterface;
use Magenest\Affiliate\Api\Data\AccountInterface;
use Magenest\Affiliate\Api\Data\AccountExtensionInterface;

/**
 * Class Account
 * @package Magenest\Affiliate\Model\Api
 */
class Account extends AbstractExtensibleModel implements
    AccountInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\Affiliate\Model\ResourceModel\Account');
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
    public function setAccountId($value)
    {
        return $this->setData(self::ACCOUNT_ID, $value);
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
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($value)
    {
        return $this->setData(self::CODE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupId()
    {
        return $this->getData(self::GROUP_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setGroupId($value)
    {
        return $this->setData(self::GROUP_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }

    /**
     * {@inheritdoc}
     */
    public function setBalance($value)
    {
        return $this->setData(self::BALANCE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getHoldingBalance()
    {
        return $this->getData(self::HOLDING_BALANCE);
    }

    /**
     * {@inheritdoc}
     */
    public function setHoldingBalance($value)
    {
        return $this->setData(self::HOLDING_BALANCE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCommission()
    {
        return $this->getData(self::TOTAL_COMMISSION);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalCommission($value)
    {
        return $this->setData(self::TOTAL_COMMISSION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalPaid()
    {
        return $this->getData(self::TOTAL_PAID);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalPaid($value)
    {
        return $this->setData(self::TOTAL_PAID, $value);
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
    public function getEmailNotification()
    {
        return $this->getData(self::EMAIL_NOTIFICATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailNotification($value)
    {
        return $this->setData(self::EMAIL_NOTIFICATION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->getData(self::PARENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setParent($value)
    {
        return $this->setData(self::PARENT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTree()
    {
        return $this->getData(self::TREE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTree($value)
    {
        return $this->setData(self::TREE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getWithdrawMethod()
    {
        return $this->getData(self::WITHDRAW_METHOD);
    }

    /**
     * {@inheritdoc}
     */
    public function setWithdrawMethod($value)
    {
        return $this->setData(self::WITHDRAW_METHOD, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getWithdrawInformation()
    {
        return $this->getData(self::WITHDRAW_INFORMATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setWithdrawInformation($value)
    {
        return $this->setData(self::WITHDRAW_INFORMATION, $value);
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
    public function getParentEmail()
    {
        return $this->getData(self::PARENT_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setParentEmail($value)
    {
        return $this->setData(self::PARENT_EMAIL, $value);
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
     * @param AccountExtensionInterface $extensionAttributes
     *
     * @return $this
     */
    public function setExtensionAttributes(
        AccountExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
