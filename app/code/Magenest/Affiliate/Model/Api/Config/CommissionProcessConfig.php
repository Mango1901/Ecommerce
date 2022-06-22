<?php


namespace Magenest\Affiliate\Model\Api\Config;

use Magenest\Affiliate\Api\Data\Config\CommissionProcessConfigInterface;

/**
 * Class CommissionProcessConfig
 * @package Magenest\Affiliate\Model\Api\Config
 */
class CommissionProcessConfig extends \Magento\Framework\DataObject implements CommissionProcessConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEarnCommissionInvoice()
    {
        return $this->getData(self::EARN_COMMISSION_INVOICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setEarnCommissionInvoice($value)
    {
        return $this->setData(self::EARN_COMMISSION_INVOICE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getHoldingDays()
    {
        return $this->getData(self::HOLDING_DAYS);
    }

    /**
     * {@inheritdoc}
     */
    public function setHoldingDays($value)
    {
        return $this->setData(self::HOLDING_DAYS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRefund()
    {
        return $this->getData(self::REFUND);
    }

    /**
     * {@inheritdoc}
     */
    public function setRefund($value)
    {
        return $this->setData(self::REFUND, $value);
    }
}
