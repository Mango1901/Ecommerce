<?php

namespace Magenest\RewardPoints\Model\ResourceModel\LifetimeAmount;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\RewardPoints\Model\ResourceModel\LifetimeAmount
 */
class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init('Magenest\RewardPoints\Model\LifetimeAmount', 'Magenest\RewardPoints\Model\ResourceModel\LifetimeAmount');
    }
}