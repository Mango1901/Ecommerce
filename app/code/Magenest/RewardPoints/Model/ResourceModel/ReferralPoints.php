<?php

namespace Magenest\RewardPoints\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ReferralPoints
 * @package Magenest\RewardPoints\Model\ResourceModel
 */
class ReferralPoints extends AbstractDb
{
    public function _construct()
    {
        $this->_init('magenest_rewardpoints_referral_points', 'entity_id');
    }
}