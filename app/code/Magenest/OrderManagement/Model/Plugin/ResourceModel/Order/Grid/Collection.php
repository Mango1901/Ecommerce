<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Model\Plugin\ResourceModel\Order\Grid;

use Magento\Framework\DB\Select;
use Magenest\OrderManagement\Model\Order;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Api\SimpleDataObjectConverter;

/**
 * Class Collection
 * @package MMagenest\OrderManagement\Model\Plugin\ResourceModel\Order\Grid
 */
class Collection
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var Order
     */
    protected $_order;

    /**
     * Constructor.
     *
     * @param RequestInterface $request
     * @param Order $order
     */
    function __construct(
        RequestInterface $request,
        Order $order
    ) {
        $this->_request = $request;
        $this->_order = $order;
    }

    /**
     * Before Load With Filter
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\Grid\Collection
     * @param bool $printQuery
     * @param bool $logQuery
     * @return array
     */
    public function beforeLoadWithFilter(
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $subject,
        $printQuery = false,
        $logQuery = false
    ) {
        $subject->getSelect()->joinLeft(
            ['soa' => $subject->getTable("sales_order_address")],
            "main_table.entity_id = soa.parent_id AND soa.address_type = '" . \Magento\Sales\Model\Order\Address::TYPE_SHIPPING . "'",
            [
                "telephone",
                'street' => "soa.street",
                'district_ward' => new \Zend_Db_Expr(sprintf('%s, %s',
                        new \Zend_Db_Expr('soa.ward'),
                        new \Zend_Db_Expr("soa.district")
                    )
                ),
                'city' => "soa.city"
            ]
        );
        $subject->getSelect()->joinLeft(
            ['soi' => new \Zend_Db_Expr("(SELECT order_id, GROUP_CONCAT(sku, \" x \", CONVERT(qty_ordered, UNSIGNED) SEPARATOR \" --- \") as skus FROM `sales_order_item` WHERE parent_item_id is null GROUP BY order_id)")],
            "main_table.entity_id = soi.order_id",
            ["skus"]
        );
        $subject->getSelect()->joinLeft(
            ['sosh' => new \Zend_Db_Expr("(SELECT parent_id, comment, user_id FROM `sales_order_status_history` WHERE entity_id in (SELECT MAX(entity_id) FROM sales_order_status_history GROUP BY parent_id))")],
            "main_table.entity_id = sosh.parent_id",
            ["comment", "user_id"]
        );
        $type = $this->_request->getParam('type');

        if ($type && in_array($type, $this->_order->getStaffLists())) {
            $this->{'addFilter' . SimpleDataObjectConverter::snakeCaseToUpperCamelCase($type)}($subject);
        }

        return [$printQuery, $logQuery];
    }

    /**
     * Add filter customer service
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection
     * @throws \Zend_Db_Select_Exception
     */
    protected function addFilterCustomerService(\Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection)
    {
        $whereParts = $collection->getSelect()->getPart(Select::WHERE);

        foreach ($whereParts as $key => $wherePart) {
            if (strpos($wherePart, '(`type` = ') !== false) {
                $condition = '(\'' . implode('\',\'', ['pending', Order::RECEIVED_GOODS_CODE, Order::SUPPLIER_REJECTED_CODE]) . '\')';
                $whereParts[$key] = "((`status` IN {$condition})" .
                    " OR (`status` = 'processing' AND payment_method = 'alepay')" .
                    " OR (`status` = 'processing' AND payment_method = 'alepay_atm')" . " OR (`status` = 'holded' AND NOW() >= DATE_ADD(hold_unpaid_order_at, INTERVAL 24 HOUR)))";
                break;
            }
        }

        $collection->getSelect()->setPart(Select::WHERE, $whereParts);
    }

    /**
     * Add filter supplier
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection
     * @throws \Zend_Db_Select_Exception
     */
    protected function addFilterSupplier(\Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection)
    {
        $whereParts = $collection->getSelect()->getPart(Select::WHERE);

        foreach ($whereParts as $key => $wherePart) {
            if (strpos($wherePart, '(`type` = ') !== false) {
                $condition = '(\'' . implode('\',\'', [Order::WAIT_SUPPLIER_CODE]) . '\')';
                $whereParts[$key] = "(`status` IN {$condition})";
                break;
            }
        }

        $collection->getSelect()->setPart(Select::WHERE, $whereParts);
    }

    /**
     * Add filter warehouse
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection
     * @throws \Zend_Db_Select_Exception
     */
    protected function addFilterWarehouse(\Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection)
    {
        $whereParts = $collection->getSelect()->getPart(Select::WHERE);

        foreach ($whereParts as $key => $wherePart) {
            if (strpos($wherePart, '(`type` = ') !== false) {
                $condition = '(\'' . implode('\',\'', ['shipping', Order::CONFIRMED_PAID_CODE, Order::CONFIRMED_COD_CODE, 'complete']) . '\')';
                $whereParts[$key] = "(`status` IN {$condition})";
                break;
            }
        }

        $collection->getSelect()->setPart(Select::WHERE, $whereParts);
    }

    /**
     * Add filter accounting
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection
     * @throws \Zend_Db_Select_Exception
     */
    protected function addFilterAccounting(\Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection)
    {
        $whereParts = $collection->getSelect()->getPart(Select::WHERE);

        foreach ($whereParts as $key => $wherePart) {
            if (strpos($wherePart, '(`type` = ') !== false) {
                $condition = '(\'' . implode('\',\'', [Order::CONFIRMED_WAREHOUSE_SALES_CODE]) . '\')';
                $whereParts[$key] = "(`status` IN {$condition}) OR (`payment_method` = 'poscash' AND `status` = 'complete_shipment')";
                break;
            }
        }

        $collection->getSelect()->setPart(Select::WHERE, $whereParts);
    }
}
