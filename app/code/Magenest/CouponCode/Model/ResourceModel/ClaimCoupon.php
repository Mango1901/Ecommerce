<?php

namespace Magenest\CouponCode\Model\ResourceModel;

class ClaimCoupon extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init($this->getTable('magenest_customer_coupon'), 'id');
    }
}
