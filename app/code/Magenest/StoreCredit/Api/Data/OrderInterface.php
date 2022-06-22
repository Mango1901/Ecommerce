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
 * Interface OrderInterface
 * @api
 */
interface OrderInterface extends StoreCreditInterface
{
    const MP_STORE_CREDIT_EXTRA_CONTENT = 'mp_store_credit_extra_content';

    /**
     * @return string
     */
    public function getMpStoreCreditExtraContent();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMpStoreCreditExtraContent($value);
}
