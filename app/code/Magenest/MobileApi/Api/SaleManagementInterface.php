<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Api;

/**
 * Interface SaleManagementInterface
 * @package Magenest\MobileApi\Api
 */
interface SaleManagementInterface
{
    /**
     * Get all order by customer
     *
     * @param int $customerId
     * @param string|null $page
     * @param string|null $status
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     */
    public function getOrders($customerId, string $page = null, string $status = null);

    /**
     * Get order shipments
     *
     * @param int $orderId
     *
     * @return \Magento\Sales\Api\Data\ShipmentSearchResultInterface
     */
    public function getOrderShipments($orderId);

    /**
     * Get order invoices
     *
     * @param int $orderId
     *
     * @return \Magento\Sales\Api\Data\InvoiceSearchResultInterface
     */
    public function getOrderInvoices($orderId);

    /**
     * Lists orders that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria The search criteria.
     *
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface Order search result interface.
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
