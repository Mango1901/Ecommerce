<?php

namespace Magenest\RewardPoints\Model\ResourceModel\Expired;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\RewardPoints\Model\ResourceModel\Expired
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
        $this->_init('Magenest\RewardPoints\Model\Expired', 'Magenest\RewardPoints\Model\ResourceModel\Expired');
    }
}
