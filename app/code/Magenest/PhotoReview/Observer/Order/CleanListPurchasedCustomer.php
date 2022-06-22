<?php

namespace Magenest\PhotoReview\Observer\Order;

use Magenest\PhotoReview\CustomerData\SaleOrderItemOfCustomer;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

/**
 * Class CleanListPurchasedCustomer
 */
class CleanListPurchasedCustomer implements ObserverInterface
{
    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if (!$order->getCustomerIsGuest()) {
            $customerId = $order->getCustomerId();
            if ($customerId) {
                $tags = [SaleOrderItemOfCustomer::CACHE_KEY_PREFIX . $customerId];
                $this->cache->clean($tags);
            }
        }
    }
}
