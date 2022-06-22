<?php
namespace Magenest\RewardPoints\Model\ResourceModel\ReferralCoupon;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init('Magenest\RewardPoints\Model\ReferralCoupon','Magenest\RewardPoints\Model\ResourceModel\ReferralCoupon');
    }
}