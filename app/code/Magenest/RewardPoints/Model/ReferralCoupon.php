<?php
namespace Magenest\RewardPoints\Model;

class ReferralCoupon extends \Magento\Framework\Model\AbstractModel {
    public function _construct()
    {
        $this->_init('Magenest\RewardPoints\Model\ResourceModel\ReferralCoupon');
    }
}