<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics;

/**
 * Class Manufacturer
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class Manufacturer extends AbstractResource
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_manufacturer', 'id');
    }

    /**
     * {@inheritdoc}
     */
    protected function process()
    {
        $period = $this->getPeriod('order.created_at');
        $columns = [
            'period' => $period,
            'store_id' => 'order.store_id',
            'order_status' => 'order.status',
            'manufacturer' => 'IFNULL(manufacturer_value.value, "Not Set")',
            'order_items_count' => 'SUM(COALESCE(main_table.qty_ordered, 0))',
            'subtotal' => 'SUM(COALESCE(configurable.base_row_total, main_table.base_row_total, 0.0))',
            'tax' => 'SUM(COALESCE(configurable.base_tax_amount, main_table.base_tax_amount, 0.0))',
            'discount' => 'ABS(SUM(COALESCE(configurable.base_discount_amount, main_table.base_discount_amount, 0.0)))',
            'total' => '(SUM(COALESCE(configurable.base_row_total, main_table.base_row_total, 0.0))
                + SUM(COALESCE(configurable.base_tax_amount, main_table.base_tax_amount, 0.0))
                - SUM(COALESCE(configurable.base_discount_amount, main_table.base_discount_amount, 0.0)))',
            'invoiced' => '(SUM(COALESCE(configurable.base_row_invoiced, main_table.base_row_invoiced, 0.0))
                + SUM(COALESCE(configurable.base_tax_invoiced, main_table.base_tax_invoiced, 0.0))
                - SUM(COALESCE(configurable.base_discount_invoiced, main_table.base_discount_invoiced, 0.0)))',
            'refunded' => '(SUM(COALESCE(configurable.base_amount_refunded, main_table.base_amount_refunded, 0.0)) '
                . ' + SUM(COALESCE(configurable.base_tax_refunded, main_table.base_tax_refunded, 0.0)) '
                . ' - SUM(COALESCE(configurable.base_discount_refunded, main_table.base_discount_refunded, 0.0)))',
            'to_global_rate' => 'order.base_to_global_rate'
        ];

        $orderItemTable = $this->getTable('sales_order_item');

        /* @var $manufacturerAttr \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $manufacturerAttr = $this->attributeRepository->get('catalog_product', 'manufacturer');
        $manufacturerTable = $manufacturerAttr->getBackendTable();

        $select = $this->getConnection()->select()
            ->from(['main_table' => $orderItemTable], [])
            ->columns($columns)
            ->joinLeft(
                ['item_manufacturer' => $manufacturerTable],
                'item_manufacturer.' . $this->getCatalogLinkField() . ' = main_table.product_id '
                . 'AND item_manufacturer.attribute_id = ' . $manufacturerAttr->getId(),
                []
            )->joinLeft(
                ['manufacturer_value' => $this->getTable('eav_attribute_option_value')],
                'item_manufacturer.value = manufacturer_value.option_id AND manufacturer_value.store_id = 0',
                []
            )->joinLeft(
                ['order' => $this->getTable('sales_order')],
                'order.entity_id = main_table.order_id',
                []
            )->joinLeft(
                ['bundle_item_price' => $this->getBundleItemsPrice()],
                '(order.entity_id = bundle_item_price.order_id AND '
                . 'main_table.item_id = bundle_item_price.parent_item_id)',
                []
            )->joinLeft(
                ['configurable' => $orderItemTable],
                '(order.entity_id = configurable.order_id AND main_table.parent_item_id = configurable.item_id '
                . 'AND configurable.product_type IN ("configurable"))',
                []
            )
            ->joinLeft(
                ['parent' => $orderItemTable],
                '(order.entity_id = parent.order_id AND main_table.parent_item_id = parent.item_id)',
                []
            )
            ->where('(main_table.product_type <> "bundle" OR bundle_item_price.price = 0)')
            ->where('(main_table.product_type <> "configurable")')
            ->group(
                [
                    'order.status',
                    $period,
                    'order.store_id',
                    'item_manufacturer.value',
                    'order.base_to_global_rate'
                ]
            );
        $select = $this->addFilterByCreatedAt($select, 'order');

        $this->getConnection()->query($select->insertFromSelect($this->getIdxTable(), array_keys($columns)));
    }
}
