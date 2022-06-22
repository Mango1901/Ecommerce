<?php
namespace Magenest\RewardPoints\Model\ResourceModel;

class MyReferral extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
    public function _construct()
    {
        $this->_init('magenest_rewardpoints_my_referral','id');
    }
}