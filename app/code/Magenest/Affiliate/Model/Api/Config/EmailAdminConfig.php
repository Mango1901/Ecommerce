<?php


namespace Magenest\Affiliate\Model\Api\Config;

use Magenest\Affiliate\Api\Data\Config\EmailAdminConfigInterface;

/**
 * Class EmailConfig
 * @package Magenest\Affiliate\Model\Api\Config
 */
class EmailAdminConfig extends \Magento\Framework\DataObject implements EmailAdminConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEmailsTo()
    {
        return $this->getData(self::EMAILS_TO);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailsTo($value)
    {
        return $this->setData(self::EMAILS_TO, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEnableSignUp()
    {
        return $this->getData(self::ENABLE_SIGN_UP);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnableSignUp($value)
    {
        return $this->setData(self::ENABLE_SIGN_UP, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSignUpTemplate()
    {
        return $this->getData(self::SIGN_UP_TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSignUpTemplate($value)
    {
        return $this->setData(self::SIGN_UP_TEMPLATE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEnableWithdrawRequest()
    {
        return $this->getData(self::ENABLE_WITHDRAW_REQUEST);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnableWithdrawRequest($value)
    {
        return $this->setData(self::ENABLE_WITHDRAW_REQUEST, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getWithdrawRequestTemplate()
    {
        return $this->getData(self::WITHDRAW_REQUEST_TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setWithdrawRequestTemplate($value)
    {
        return $this->setData(self::WITHDRAW_REQUEST_TEMPLATE, $value);
    }
}
