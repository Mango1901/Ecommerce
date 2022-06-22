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

namespace Magenest\StoreCredit\Controller\Adminhtml\Transaction;

use Magenest\StoreCredit\Controller\Adminhtml\AbstractTransaction;

/**
 * Class NewAction
 * @package Magenest\StoreCredit\Controller\Adminhtml\Transaction
 */
class NewAction extends AbstractTransaction
{
    /**
     * Forward to edit form
     */
    public function execute()
    {
        $this->_forward('view');
    }
}
