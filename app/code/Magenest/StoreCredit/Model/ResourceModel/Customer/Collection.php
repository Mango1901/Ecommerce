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

namespace Magenest\StoreCredit\Model\ResourceModel\Customer;

use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Magenest\StoreCredit\Api\Data\StoreCreditCustomerSearchResultInterface;

/**
 * Class Collection
 * @package Magenest\StoreCredit\Model\ResourceModel\Customer
 */
class Collection extends AbstractCollection implements StoreCreditCustomerSearchResultInterface
{
    /**
     * @type string
     */
    protected $_idFieldName = 'customer_id';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('Magenest\StoreCredit\Model\Customer', 'Magenest\StoreCredit\Model\ResourceModel\Customer');
    }
}
