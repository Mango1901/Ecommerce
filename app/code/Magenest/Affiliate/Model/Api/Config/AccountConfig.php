<?php


namespace Magenest\Affiliate\Model\Api\Config;

use Magenest\Affiliate\Api\Data\Config\AccountConfigInterface;

/**
 * Class AccountConfig
 * @package Magenest\Affiliate\Model\Api\Config
 */
class AccountConfig extends \Magento\Framework\DataObject implements AccountConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSignUpDefaultGroup()
    {
        return $this->getData(self::SIGN_UP_DEFAULT_GROUP);
    }

    /**
     * {@inheritdoc}
     */
    public function setSignUpDefaultGroup($value)
    {
        return $this->setData(self::SIGN_UP_DEFAULT_GROUP, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSignUpAdminApproved()
    {
        return $this->getData(self::SIGN_UP_ADMIN_APPROVED);
    }

    /**
     * {@inheritdoc}
     */
    public function setSignUpAdminApproved($value)
    {
        return $this->setData(self::SIGN_UP_ADMIN_APPROVED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSignUpDefaultEmailNotification()
    {
        return $this->getData(self::SIGN_UP_DEFAULT_EMAIL_NOTIFICATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setSignUpDefaultEmailNotification($value)
    {
        return $this->setData(self::SIGN_UP_DEFAULT_EMAIL_NOTIFICATION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTermConditionsEnable()
    {
        return $this->getData(self::TERM_CONDITIONS_ENABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTermConditionsEnable($value)
    {
        return $this->setData(self::TERM_CONDITIONS_ENABLE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTermConditionsCheckboxText()
    {
        return $this->getData(self::TERM_CONDITIONS_CHECKBOX_TEXT);
    }

    /**
     * {@inheritdoc}
     */
    public function setTermConditionsCheckboxText($value)
    {
        return $this->setData(self::TERM_CONDITIONS_CHECKBOX_TEXT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTermConditionsTitle()
    {
        return $this->getData(self::TERM_CONDITIONS_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTermConditionsTitle($value)
    {
        return $this->setData(self::TERM_CONDITIONS_TITLE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTermConditionsHtml()
    {
        return $this->getData(self::TERM_CONDITIONS_HTML);
    }

    /**
     * {@inheritdoc}
     */
    public function setTermConditionsHtml($value)
    {
        return $this->setData(self::TERM_CONDITIONS_HTML, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTermConditionsDefaultCheckbox()
    {
        return $this->getData(self::TERM_CONDITIONS_DEFAULT_CHECKBOX);
    }

    /**
     * {@inheritdoc}
     */
    public function setTermConditionsDefaultCheckbox($value)
    {
        return $this->setData(self::TERM_CONDITIONS_DEFAULT_CHECKBOX, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBalanceLimit()
    {
        return $this->getData(self::BALANCE_LIMIT);
    }

    /**
     * {@inheritdoc}
     */
    public function setBalanceLimit($value)
    {
        return $this->setData(self::BALANCE_LIMIT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBalanceNegative()
    {
        return $this->getData(self::BALANCE_NEGATIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setBalanceNegative($value)
    {
        return $this->setData(self::BALANCE_NEGATIVE, $value);
    }

}
