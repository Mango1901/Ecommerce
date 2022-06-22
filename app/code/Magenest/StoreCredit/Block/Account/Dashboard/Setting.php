<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license that is
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

namespace Magenest\StoreCredit\Block\Account\Dashboard;

use Magenest\StoreCredit\Block\Account\Dashboard;
use Magenest\StoreCredit\Helper\Email;

/**
 * Class Setting
 * @package Magenest\StoreCredit\Block\Account\Dashboard
 */
class Setting extends Dashboard
{
    /**
     * @return Email
     */
    public function getEmailHelper()
    {
        return $this->helper->getEmailHelper();
    }

    /**
     * @return bool
     */
    public function getMpCreditNotification()
    {
        return !!$this->helper->getAccountHelper()->getCurrentCustomer()->getMpCreditNotification();
    }
}
