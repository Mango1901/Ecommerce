<?php

namespace Magenest\RewardPoints\Model\ResourceModel\Referral;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\RewardPoints\Model\ResourceModel\Referral
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
        $this->_init('Magenest\RewardPoints\Model\Referral', 'Magenest\RewardPoints\Model\ResourceModel\Referral');
    }
}
