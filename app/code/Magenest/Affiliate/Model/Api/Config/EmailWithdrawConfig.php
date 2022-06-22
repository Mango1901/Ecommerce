<?php


namespace Magenest\Affiliate\Model\Api\Config;

use Magenest\Affiliate\Api\Data\Config\EmailWithdrawConfigInterface;

/**
 * Class EmailWithdrawConfig
 * @package Magenest\Affiliate\Model\Api\Config
 */
class EmailWithdrawConfig extends \Magento\Framework\DataObject implements EmailWithdrawConfigInterface
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
    public function getComplete()
    {
        return $this->getData(self::COMPLETE);
    }

    /**
     * {@inheritdoc}
     */
    public function setComplete($value)
    {
        return $this->setData(self::COMPLETE, $value);
    }
}
