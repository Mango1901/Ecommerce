<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Api\Data;

/**
 * Interface StoreCreditInterface
 * @api
 */
interface StoreCreditInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const MP_STORE_CREDIT_DISCOUNT      = 'mp_store_credit_discount';
    const MP_STORE_CREDIT_BASE_DISCOUNT = 'mp_store_credit_base_discount';

    /**
     * @return float
     */
    public function getMpStoreCreditDiscount();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setMpStoreCreditDiscount($value);

    /**
     * @return float
     */
    public function getMpStoreCreditBaseDiscount();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setMpStoreCreditBaseDiscount($value);
}
