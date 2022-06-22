<?php

namespace Magenest\SellOnInstagram\Observer\Checkout;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class TrackOrder implements ObserverInterface
{
    const STATUS_FROM_SHOP = 1;
    /**
     * @var ResourceConnection
     */
    protected $resource;

    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    public function execute(Observer $observer)
    {
        $quotes = $observer->getData('quote');
        $order = $observer->getData('order');
        $quoteItems = $quotes->getItems();
        $senderId = [];
        if (!$quoteItems && is_array($quoteItems)) {
            foreach ($quoteItems as $quoteItem) {
                $senderId [] = $quoteItem->getData('from_shop');
            }
            if (in_array(self::STATUS_FROM_SHOP, $senderId)) {
                $connection = $this->resource->getConnection();
                $salesOrderGridTable = $this->resource->getTableName('sales_order_grid');
                $connection->update($salesOrderGridTable, ['ordered_by_shop' => 1], ["entity_id = ?" => $order->getId()]);
            }
        }
    }
}
