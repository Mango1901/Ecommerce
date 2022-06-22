<?php
namespace Magenest\RewardPoints\Model\ResourceModel;

class ReferralCoupon extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
    public function _construct()
    {
        $this->_init('magenest_rewardpoints_referral_coupons','id');
    }
}