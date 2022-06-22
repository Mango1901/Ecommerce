<?php

namespace Magenest\RewardPoints\Model\ResourceModel;

/**
 * Class Referral
 * @package Magenest\RewardPoints\Model\ResourceModel
 */
class Referral extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_rewardpoints_referral_code', 'id');
    }
}
