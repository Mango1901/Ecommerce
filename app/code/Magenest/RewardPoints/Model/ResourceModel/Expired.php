<?php

namespace Magenest\RewardPoints\Model\ResourceModel;

/**
 * Class Expired
 * @package Magenest\RewardPoints\Model\ResourceModel
 */
class Expired extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('magenest_rewardpoints_expired', 'id');
    }
}
