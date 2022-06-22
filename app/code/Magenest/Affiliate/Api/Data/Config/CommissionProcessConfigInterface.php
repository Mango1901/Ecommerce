<?php


namespace Magenest\Affiliate\Api\Data\Config;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface CommissionProcessConfigInterface
 * @api
 */
interface CommissionProcessConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const EARN_COMMISSION_INVOICE = 'earn_commission_invoice';

    const HOLDING_DAYS = 'holding_days';

    const REFUND = 'refund';

    /**
     * @return bool
     */
    public function getEarnCommissionInvoice();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setEarnCommissionInvoice($value);

    /**
     * @return int
     */
    public function getHoldingDays();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setHoldingDays($value);

    /**
     * @return bool
     */
    public function getRefund();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setRefund($value);
}
