<?php

namespace Magenest\RewardPoints\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class LifetimeAmount
 * @package Magenest\RewardPoints\Model
 */
class LifetimeAmount extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Magenest\RewardPoints\Model\ResourceModel\LifetimeAmount::class);
    }

}
