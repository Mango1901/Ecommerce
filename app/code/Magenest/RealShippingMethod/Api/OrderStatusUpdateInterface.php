<?php
namespace Magenest\RealShippingMethod\Api;

interface OrderStatusUpdateInterface
{
    /**
     * Update status for multiple orders.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface[] $entities
     *
     * @return \Magento\Sales\Api\Data\OrderInterface[]
     */
    public function save(array $entities): array;
}
