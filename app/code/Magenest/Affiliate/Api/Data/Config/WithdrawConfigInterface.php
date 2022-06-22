<?php


namespace Magenest\Affiliate\Api\Data\Config;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface WithdrawConfigInterface
 * @api
 */
interface WithdrawConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ALLOW_REQUEST = 'allow_request';

    const PAYMENT_METHOD = 'payment_method';

    const MINIMUM_BALANCE = 'minimum_balance';

    const MINIMUM = 'minimum';

    const MAXIMUM = 'maximum';

    /**
     * @return bool
     */
    public function getAllowRequest();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setAllowRequest($value);

    /**
     * @return string
     */
    public function getPaymentMethod();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPaymentMethod($value);

    /**
     * @return float
     */
    public function getMinimumBalance();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setMinimumBalance($value);

    /**
     * @return float
     */
    public function getMinimum();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setMinimum($value);

    /**
     * @return float
     */
    public function getMaximum();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setMaximum($value);
}
