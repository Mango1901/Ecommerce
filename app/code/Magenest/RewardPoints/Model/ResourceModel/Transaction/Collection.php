<?php

namespace Magenest\RewardPoints\Model\ResourceModel\Transaction;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\RewardPoints\Model\ResourceModel\Transaction
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Magenest\RewardPoints\Model\Transaction', 'Magenest\RewardPoints\Model\ResourceModel\Transaction');
    }
}
