<?php

namespace Magenest\RewardPoints\Model;

use Magenest\RewardPoints\Helper\MembershipAction;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Transaction
 * @package Magenest\RewardPoints\Model
 */
class Transaction extends AbstractModel
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_reward_points_transaction';

    /**
     *
     */
    public function _construct()
    {
        $this->_init('Magenest\RewardPoints\Model\ResourceModel\Transaction');
        $this->setIdFieldName('id');
    }
}
