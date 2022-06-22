<?php


namespace Magenest\Affiliate\Model\Api\Config;

use Magenest\Affiliate\Api\Data\Config\CommissionConfigInterface;

/**
 * Class CommissionConfig
 * @package Magenest\Affiliate\Model\Api\Config
 */
class CommissionConfig extends \Magento\Framework\DataObject implements CommissionConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getByTax()
    {
        return $this->getData(self::BY_TAX);
    }

    /**
     * {@inheritdoc}
     */
    public function setByTax($value)
    {
        return $this->setData(self::BY_TAX, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getShipping()
    {
        return $this->getData(self::SHIPPING);
    }

    /**
     * {@inheritdoc}
     */
    public function setShipping($value)
    {
        return $this->setData(self::SHIPPING, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getProcess()
    {
        return $this->getData(self::PROCESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setProcess($value)
    {
        return $this->setData(self::PROCESS, $value);
    }
}
