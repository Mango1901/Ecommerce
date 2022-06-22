<?php

namespace Magenest\RewardPoints\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class LifetimeAmount extends AbstractDb
{
    public function _construct()
    {
        $this->_init('magenest_rewardpoints_lifetime_amount', 'entity_id');
    }
}