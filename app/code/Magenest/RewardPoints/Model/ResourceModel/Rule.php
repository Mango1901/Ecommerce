<?php

namespace Magenest\RewardPoints\Model\ResourceModel;

/**
 * Class Rule
 * @package Magenest\RewardPoints\Model\ResourceModel
 */
class Rule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('magenest_rewardpoints_rule', 'id');
    }
}
