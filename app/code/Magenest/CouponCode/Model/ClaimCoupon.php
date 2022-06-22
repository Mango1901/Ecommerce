<?php

namespace Magenest\CouponCode\Model;

use Magenest\CouponCode\Model\ResourceModel\ClaimCoupon as ResourceModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magenest\CouponCode\Model\Coupon;

class ClaimCoupon extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    public const CACHE_TAG = 'MAGENEST_MY_COUPON_LISTING';

    /**
     * Init model
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Identities for cache
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG, Coupon::CACHE_TAG];
    }
}
