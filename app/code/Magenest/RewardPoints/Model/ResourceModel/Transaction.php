<?php

namespace Magenest\RewardPoints\Model\ResourceModel;

/**
 * Class Transaction
 * @package Magenest\RewardPoints\Model\ResourceModel
 */
class Transaction extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('magenest_rewardpoints_transaction', 'id');
    }
}
