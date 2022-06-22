<?php


namespace Magenest\Affiliate\Model\Api\Config;

use Magenest\Affiliate\Api\Data\Config\EmailTransactionConfigInterface;

/**
 * Class EmailTransactionConfig
 * @package Magenest\Affiliate\Model\Api\Config
 */
class EmailTransactionConfig extends \Magento\Framework\DataObject implements EmailTransactionConfigInterface
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
    public function getUpdateBalance()
    {
        return $this->getData(self::UPDATE_BALANCE);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdateBalance($value)
    {
        return $this->setData(self::UPDATE_BALANCE, $value);
    }
}
