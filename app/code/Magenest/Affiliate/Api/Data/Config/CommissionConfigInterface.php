<?php


namespace Magenest\Affiliate\Api\Data\Config;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface CommissionConfigInterface
 * @api
 */
interface CommissionConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const BY_TAX = 'by_tax';

    const SHIPPING = 'shipping';

    const PROCESS = 'process';

    /**
     * @return bool
     */
    public function getByTax();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setByTax($value);

    /**
     * @return bool
     */
    public function getShipping();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setShipping($value);

    /**
     * @return \Magenest\Affiliate\Api\Data\Config\CommissionProcessConfigInterface
     */
    public function getProcess();

    /**
     * @param \Magenest\Affiliate\Api\Data\Config\CommissionProcessConfigInterface $value
     *
     * @return $this
     */
    public function setProcess(\Magenest\Affiliate\Api\Data\Config\CommissionProcessConfigInterface $value);
}
