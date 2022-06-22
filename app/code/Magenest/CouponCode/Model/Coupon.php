<?php

namespace Magenest\CouponCode\Model;

use Magenest\CouponCode\Model\ResourceModel\Coupon as ResourceModel;
use Magento\Framework\DataObject\IdentityInterface;

class Coupon extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    public const CACHE_TAG = 'MAGENEST_COUPON_LISTING';

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
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG];
    }
}
