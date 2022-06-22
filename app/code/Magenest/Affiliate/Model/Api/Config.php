<?php


namespace Magenest\Affiliate\Model\Api;

use Magenest\Affiliate\Api\Data\ConfigInterface;

/**
 * Class Config
 * @package Magenest\Affiliate\Model\Api
 */
class Config extends \Magento\Framework\DataObject implements ConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getGeneral()
    {
        return $this->getData(self::GENERAL);
    }

    /**
     * {@inheritdoc}
     */
    public function setGeneral($value)
    {
        return $this->setData(self::GENERAL, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccount()
    {
        return $this->getData(self::ACCOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAccount($value)
    {
        return $this->setData(self::ACCOUNT, $value);
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
    public function getWithdraw()
    {
        return $this->getData(self::WITHDRAW);
    }

    /**
     * {@inheritdoc}
     */
    public function setWithdraw($value)
    {
        return $this->setData(self::WITHDRAW, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($value)
    {
        return $this->setData(self::EMAIL, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRefer()
    {
        return $this->getData(self::REFER);
    }

    /**
     * {@inheritdoc}
     */
    public function setRefer($value)
    {
        return $this->setData(self::REFER, $value);
    }
}
