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

namespace Magenest\StoreCredit\Model\Action;

use Magenest\StoreCredit\Model\Action;

/**
 * Class Revert
 * @package Magenest\StoreCredit\Model\Action
 */
class Revert extends Action
{
    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return 'Reverted from order #%1';
    }

    /**
     * @inheritdoc
     */
    public function getAction()
    {
        return __('Reverted');
    }
}
