<?php

namespace Magenest\RewardPoints\Model\ResourceModel;

/**
 * Class Account
 * @package Magenest\RewardPoints\Model\ResourceModel
 */
class Account extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('magenest_rewardpoints_account', 'id');
    }
}
