<?php

namespace Magenest\PhotoReview\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\App\Cache\Type\Collection;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Psr\Log\LoggerInterface;

/**
 * Sale Order Item Id of Customer section
 */
class SaleOrderItemOfCustomer  implements SectionSourceInterface
{
    const CACHE_KEY_PREFIX = 'product_ids_purchased_of_customer_id_';

    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param CurrentCustomer $currentCustomer
     * @param ResourceConnection $resource
     * @param LoggerInterface $logger
     * @param CacheInterface $cache
     * @param SerializerInterface $serializer
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        ResourceConnection $resource,
        LoggerInterface $logger,
        CacheInterface $cache,
        SerializerInterface $serializer
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->resource = $resource;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function getSectionData()
    {
        if (!$this->currentCustomer->getCustomerId()) {
            return [];
        }

        $customer = $this->currentCustomer->getCustomer();
        if ($customerId = $customer->getId()) {
            $cacheKey = self::CACHE_KEY_PREFIX . $customerId;
            $listProductIdPurchased = null;
            if ($dataFromCache = $this->cache->load($cacheKey)) {
                $listProductIdPurchased = $this->serializer->unserialize($dataFromCache);
            }

            if (!$listProductIdPurchased) {
                $listProductId = $this->getListProductIdPurchasedOfCustomer($customerId);
                $listProductIdPurchased = array_unique(explode(',', $listProductId));
                $this->cache->save($this->serializer->serialize($listProductIdPurchased), $cacheKey, [$cacheKey, Collection::CACHE_TAG]);
            }

            if ($listProductIdPurchased) {
                return [
                    'listProductIdPurchased' => $listProductIdPurchased
                ];
            }
        }

        return [];
    }

    /**
     * @param int $customerId
     * @return string
     */
    private function getListProductIdPurchasedOfCustomer($customerId)
    {
        try {
            $connection = $this->resource->getConnection();
            $salesOrderTable = $this->resource->getTableName('sales_order');
            $salesOrderItemTable = $this->resource->getTableName('sales_order_item');
            $select = $connection->select()
                ->from(
                    $salesOrderItemTable,
                    ['listProductId' => 'GROUP_CONCAT(' . OrderItemInterface::PRODUCT_ID . ')']
                )
                ->join(
                    $salesOrderTable,
                    $salesOrderTable . '.' . OrderInterface::ENTITY_ID . '=' . $salesOrderItemTable . '.' . OrderItemInterface::ORDER_ID,
                    []
                )
                ->group(OrderInterface::CUSTOMER_ID)
                ->having(OrderInterface::CUSTOMER_ID . ' = ' . $customerId);
            return $connection->fetchOne($select);
        } catch (\Exception $exception) {
            $this->logger->debug($exception->getMessage());
        }

        return null;
    }
}
