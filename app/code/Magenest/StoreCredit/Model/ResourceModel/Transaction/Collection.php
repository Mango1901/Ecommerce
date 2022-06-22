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

namespace Magenest\StoreCredit\Model\ResourceModel\Transaction;

use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Magenest\StoreCredit\Api\Data\TransactionSearchResultInterface;

/**
 * Class Collection
 * @package Magenest\StoreCredit\Model\ResourceModel\Transaction
 */
class Collection extends AbstractCollection implements TransactionSearchResultInterface
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'transaction_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_store_credit_transaction_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'store_credit_transaction_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magenest\StoreCredit\Model\Transaction',
            'Magenest\StoreCredit\Model\ResourceModel\Transaction'
        );
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->join(
            ['customer' => $this->getTable('customer_entity')],
            'main_table.customer_id = customer.entity_id',
            ['email']
        );

        return $this;
    }

    /**
     * @param $field
     * @param null $condition
     *
     * @return AbstractCollection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'created_at') {
            $field = 'main_table.created_at';
        }

        return parent::addFieldToFilter($field, $condition);
    }
}
