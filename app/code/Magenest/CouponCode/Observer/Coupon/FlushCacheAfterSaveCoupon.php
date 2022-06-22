<?php

namespace Magenest\CouponCode\Observer\Coupon;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\PageCache\Model\Cache\Type;
use Magenest\CouponCode\Block\Coupon;
use Magenest\CouponCode\Block\MyCoupon;

class FlushCacheAfterSaveCoupon implements ObserverInterface
{
    /**
     * @var Type
     */
    protected $flush;

    /**
     * @param Type $flush
     */
    public function __construct(
        Type $flush
    ) {
        $this->flush = $flush;
    }

    /**
     * Flush cache when save a coupon
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $this->flush->clean(\Zend_Cache::CLEANING_MODE_ALL, [Coupon::CACHE_TAG,MyCoupon::CACHE_TAG]);
    }
}
