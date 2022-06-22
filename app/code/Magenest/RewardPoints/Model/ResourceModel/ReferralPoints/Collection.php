<?php

namespace Magenest\RewardPoints\Model\ResourceModel\ReferralPoints;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\RewardPoints\Model\ResourceModel\ReferralPoints
 */
class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init('Magenest\RewardPoints\Model\ReferralPoints', 'Magenest\RewardPoints\Model\ResourceModel\ReferralPoints');
    }
}