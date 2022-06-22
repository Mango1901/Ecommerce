<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics;

use Magento\Customer\Model\Group as CustomerGroup;

/**
 * Class SalesDetailed
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class SalesDetailed extends AbstractResource
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_sales_detailed', 'id');
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function process()
    {
        $period = $this->getPeriod('main_table.created_at');
        $columns = [
            'period' => $period,
            'store_id' => 'main_table.store_id',
            'order_status' => 'main_table.status',
            'order_id' => 'main_table.entity_id',
            'order_increment_id' => 'main_table.increment_id',
            'order_date' => 'main_table.created_at',
            'product_id' => 'IFNULL(item.product_id, item2.product_id)',
            'product_name' => 'item.name',
            'sku' => 'IFNULL(item.sku, catalog_product.sku)',
            'manufacturer' => 'IFNULL(manufacturer_value.value, "Not Set")',
            'customer_id' => 'main_table.customer_id',
            'customer_email' => 'main_table.customer_email',
            'customer_name' => 'IFNULL(CONCAT(main_table.customer_firstname, " ", main_table.customer_lastname), '
                . 'CONCAT(billing_address.firstname, " ", billing_address.lastname))',
            'customer_group' => 'c_group.customer_group_code',
            'country' => 'COALESCE(shipping_address.country_id, billing_address.country_id, "")',
            'region' => 'COALESCE(shipping_address.region, billing_address.region, "")',
            'city' => 'COALESCE(shipping_address.city, billing_address.city, "")',
            'zip_code' => 'COALESCE(shipping_address.postcode, billing_address.postcode, "")',
            'address' => 'COALESCE(shipping_address.street, billing_address.street, "")',
            'phone' => 'COALESCE(shipping_address.telephone, billing_address.telephone, "")',
            'coupon_code' => 'main_table.coupon_code',
            'qty_ordered' => 'COALESCE(IFNULL(item.qty_ordered, item2.qty_ordered), 0)',
            'qty_invoiced' => 'COALESCE(IFNULL(item.qty_invoiced, item2.qty_invoiced), 0)',
            'qty_shipped' => 'COALESCE(IFNULL(item.qty_shipped, item2.qty_shipped), 0)',
            'qty_refunded' => 'COALESCE(IFNULL(item.qty_refunded, item2.qty_refunded), 0)',
            'item_price' => 'COALESCE(IFNULL(item.base_price, item2.base_price), 0)',
            'item_cost' => new \Zend_Db_Expr(
                'CASE item.product_type '
                . 'WHEN "configurable" THEN COALESCE(SUM(item2.base_cost), item.base_cost, 0) '
                // If bundle with fixed price or bundle with dynamic price
                . 'WHEN "bundle" THEN IF(bundle_item_price.price = 0, '
                . 'COALESCE(item.base_cost, SUM(item2.base_cost), 0), COALESCE(SUM(item2.base_cost), 0)) '
                . 'ELSE item.base_cost END'
            ),
            'subtotal' => 'COALESCE((IFNULL(item.qty_ordered, item2.qty_ordered) '
                . '* IFNULL(item.base_price, item2.base_price)), 0)',
            'discount' => 'COALESCE((IF(item.base_discount_amount = 0, SUM(item2.base_discount_amount), '
                . 'item.base_discount_amount)), 0)',
            'tax' => 'COALESCE((IFNULL(item.base_tax_amount, item2.base_tax_amount)), 0)',
            'total' => 'COALESCE((IFNULL(item.base_row_total, item2.base_row_total) '
                . '+ IFNULL(item.base_discount_tax_compensation_amount, 0.0000) '
                . '+ IFNULL(COALESCE(item.base_weee_tax_applied_amount, 0), '
                . 'COALESCE(item2.base_weee_tax_applied_amount, 0)) '
                . '- COALESCE(IF(item.base_discount_amount = 0, SUM(item2.base_discount_amount), '
                . 'item.base_discount_amount), 0)), 0)',
            'total_incl_tax' => '(IFNULL(item.base_row_total, item2.base_row_total) '
                . '+ IFNULL(item.base_tax_amount, item2.base_tax_amount) '
                . '+ IFNULL(item.base_discount_tax_compensation_amount, 0.0000) '
                . '+ IFNULL(COALESCE(item.base_weee_tax_applied_amount, 0), '
                . 'COALESCE(item2.base_weee_tax_applied_amount, 0)) '
                . '- COALESCE(IF(item.base_discount_amount = 0, SUM(item2.base_discount_amount), '
                . 'item.base_discount_amount), 0))',
            'invoiced' => 'COALESCE((COALESCE(IF(item.product_type = "bundle" AND item2.base_price > 0, '
                . 'SUM(item2.base_row_invoiced), NULL), item.base_row_invoiced, item2.base_row_invoiced) '
                . '+ IFNULL(COALESCE(item.base_discount_tax_compensation_invoiced, 0), '
                . 'COALESCE(item2.base_discount_tax_compensation_invoiced, 0)) '
                . '- COALESCE(IF(item.product_type = "bundle" AND item2.base_price > 0, '
                . 'SUM(item2.base_discount_invoiced), NULL), item.base_discount_invoiced, '
                . 'item2.base_discount_invoiced)), 0)',
            'tax_invoiced' => 'COALESCE(IF(item.product_type = "bundle" AND item2.base_price > 0, '
                . 'SUM(item2.base_tax_invoiced), NULL), item.base_tax_invoiced, item2.base_tax_invoiced, 0)',
            'invoiced_incl_tax' => 'COALESCE((COALESCE(IF(item.product_type = "bundle" AND item2.base_price > 0, '
                . 'SUM(item2.base_row_invoiced), NULL), item.base_row_invoiced, item2.base_row_invoiced) '
                . '+ IFNULL(COALESCE(item.base_discount_tax_compensation_invoiced, 0), '
                . 'COALESCE(item2.base_discount_tax_compensation_invoiced, 0)) '
                . '+ COALESCE(IF(item.product_type = "bundle" AND item2.base_price > 0, '
                . 'SUM(item2.base_tax_invoiced), NULL), item.base_tax_invoiced, item2.base_tax_invoiced) '
                . '- COALESCE(IF(item.product_type = "bundle" AND item2.base_price > 0, '
                . 'SUM(item2.base_discount_invoiced), NULL), item.base_discount_invoiced, '
                . 'item2.base_discount_invoiced)), 0)',
            'refunded' => 'COALESCE(((IF((IFNULL(item.qty_refunded, item2.qty_refunded) > 0), 1, 0) '
                . '* ((IFNULL(item.qty_refunded, item2.qty_refunded) / IFNULL(item.qty_invoiced, item2.qty_invoiced)) '
                . '* (IFNULL(item.qty_invoiced, item2.qty_invoiced) * IFNULL(item.base_price, item2.base_price) '
                . '- ABS(COALESCE(IF(item.base_discount_amount = 0, SUM(item2.base_discount_amount), '
                . 'item.base_discount_amount), 0)))))), 0)',
            'tax_refunded' => 'COALESCE(IF((IFNULL(item.qty_refunded, item2.qty_refunded) > 0), '
                . '(IFNULL(item.qty_refunded, item2.qty_refunded) '
                . '/ IFNULL(item.qty_invoiced, item2.qty_invoiced) '
                . '* IFNULL(item.base_tax_invoiced, item2.base_tax_invoiced)), 0), 0)',
            'refunded_incl_tax' => 'COALESCE(((IF((IFNULL(item.qty_refunded, item2.qty_refunded) > 0), 1, 0) '
                . '* ((IFNULL(item.qty_refunded, item2.qty_refunded) * (IFNULL(item.qty_invoiced, item2.qty_invoiced) '
                . '* IFNULL(item.base_price, item2.base_price) - ABS(COALESCE(IF(item.base_discount_amount = 0, '
                . 'SUM(item2.base_discount_amount), item.base_discount_amount), 0))) '
                . '/ IFNULL(item.qty_invoiced, item2.qty_invoiced)) '
                . '+ IF((IFNULL(item.qty_refunded, item2.qty_refunded) > 0), '
                . '(IFNULL(item.qty_refunded, item2.qty_refunded) / IFNULL(item.qty_invoiced, item2.qty_invoiced) '
                . '* IFNULL(item.base_tax_invoiced, item2.base_tax_invoiced)), 0)))), 0)',
            'to_global_rate' => 'main_table.base_to_global_rate'
        ];

        /* @var $manufacturerAttr \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $manufacturerAttr = $this->attributeRepository->get('catalog_product', 'manufacturer');
        $manufacturerTable = $manufacturerAttr->getBackendTable();

        $orderItemTable = $this->getTable('sales_order_item');
        $salesOrderAddress = $this->getTable('sales_order_address');
        $select = $this->getConnection()->select()
            ->from(['main_table' => $this->getTable('sales_order')], [])
            ->join(
                ['item' => $orderItemTable],
                '(item.order_id = main_table.entity_id AND item.parent_item_id IS NULL)',
                []
            )->joinLeft(
                ['item2' => $orderItemTable],
                '(item2.order_id = main_table.entity_id AND item2.parent_item_id IS NOT NULL AND '
                . 'item2.parent_item_id = item.item_id AND item.product_type IN ("configurable", "bundle"))',
                []
            )->joinLeft(
                ['bundle_item_price' => $this->getBundleItemsPrice()],
                '(main_table.entity_id = bundle_item_price.order_id AND '
                . 'item.item_id = bundle_item_price.parent_item_id)',
                []
            )->joinLeft(
                ['catalog_product' => $this->getTable('catalog_product_entity')],
                'item.product_id = catalog_product.entity_id',
                []
            )->joinLeft(
                ['customer' => $this->getTable('customer_entity')],
                'main_table.customer_id = customer.entity_id',
                []
            )->joinLeft(
                ['c_group' => $this->getTable('customer_group')],
                'IFNULL(customer.group_id, ' . CustomerGroup::NOT_LOGGED_IN_ID . ') = c_group.customer_group_id',
                []
            )->joinLeft(
                ['shipping_address' => $salesOrderAddress],
                'shipping_address.parent_id = main_table.entity_id AND shipping_address.address_type = "shipping"',
                []
            )->joinLeft(
                ['billing_address' => $salesOrderAddress],
                'billing_address.parent_id = main_table.entity_id AND billing_address.address_type = "billing"',
                []
            )->joinLeft(
                ['item_manufacturer' => $manufacturerTable],
                'item_manufacturer.' . $this->getCatalogLinkField() . ' = item.product_id '
                . 'AND item_manufacturer.attribute_id = ' . $manufacturerAttr->getId(),
                []
            )->joinLeft(
                ['manufacturer_value' => $this->getTable('eav_attribute_option_value')],
                'item_manufacturer.value = manufacturer_value.option_id AND manufacturer_value.store_id = 0',
                []
            )->columns($columns)
            ->group('item.item_id');
        $select = $this->addFilterByCreatedAt($select, 'main_table');

        $this->getConnection()->query($select->insertFromSelect($this->getIdxTable(), array_keys($columns)));
    }
}
