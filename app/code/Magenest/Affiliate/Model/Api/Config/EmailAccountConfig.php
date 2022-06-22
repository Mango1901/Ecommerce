<?php


namespace Magenest\Affiliate\Model\Api\Config;

use Magenest\Affiliate\Api\Data\Config\EmailAccountConfigInterface;

/**
 * Class EmailAccountConfig
 * @package Magenest\Affiliate\Model\Api\Config
 */
class EmailAccountConfig extends \Magento\Framework\DataObject implements EmailAccountConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEnable()
    {
        return $this->getData(self::ENABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnable($value)
    {
        return $this->setData(self::ENABLE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getWelcome()
    {
        return $this->getData(self::WELCOME);
    }

    /**
     * {@inheritdoc}
     */
    public function setWelcome($value)
    {
        return $this->setData(self::WELCOME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getApprove()
    {
        return $this->getData(self::APPROVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setApprove($value)
    {
        return $this->setData(self::APPROVE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEnableRejection()
    {
        return $this->getData(self::ENABLE_REJECTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnableRejection($value)
    {
        return $this->setData(self::ENABLE_REJECTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRejectionTemplate()
    {
        return $this->getData(self::REJECTION_TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setRejectionTemplate($value)
    {
        return $this->setData(self::REJECTION_TEMPLATE, $value);
    }


    /**
     * {@inheritdoc}
     */
    public function getEnableStatus()
    {
        return $this->getData(self::ENABLE_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnableStatus($value)
    {
        return $this->setData(self::ENABLE_STATUS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusTemplate()
    {
        return $this->getData(self::STATUS_TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatusTemplate($value)
    {
        return $this->setData(self::STATUS_TEMPLATE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEnableWithdrawCancel()
    {
        return $this->getData(self::ENABLE_WITHDRAW_CANCEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnableWithdrawCancel($value)
    {
        return $this->setData(self::ENABLE_WITHDRAW_CANCEL, $value);
    }
}
