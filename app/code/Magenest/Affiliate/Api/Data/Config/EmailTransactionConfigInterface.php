<?php


namespace Magenest\Affiliate\Api\Data\Config;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface EmailTransactionConfigInterface
 * @api
 */
interface EmailTransactionConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENABLE = 'enable';

    const UPDATE_BALANCE = 'update_balance';

    /**
     * @return bool
     */
    public function getEnable();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setEnable($value);

    /**
     * @return string
     */
    public function getUpdateBalance();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUpdateBalance($value);
}
